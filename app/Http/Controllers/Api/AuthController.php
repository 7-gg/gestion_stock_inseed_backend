<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponses;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RequestLoginCodeRequest;
use App\Http\Requests\Auth\VerifyLoginCodeRequest;
use App\Http\Resources\UserResource;
use App\Services\LoginCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AuthController extends Controller
{
    use ApiResponses;

    public function __construct(private LoginCodeService $loginCodeService) {}

    /**
     * Première étape : Demander un code de connexion par email
     */
    public function requestLoginCode(RequestLoginCodeRequest $request)
    {
        $email = $request->validated()['email'];
        $this->loginCodeService->sendLoginCode($email);

        // Réponse uniforme pour la sécurité
        return $this->success(
            null,
            'Si votre compte est actif
            vous recevrez un code de connexion. 
            En cas de soucis contactez l\'administrateur'
        );
    }

    /**
     * Deuxième étape : Vérifier le code et connecter l'utilisateur
     */
    public function verifyLoginCode(VerifyLoginCodeRequest $request)
    {
        $validated = $request->validated();
        $email = $validated['email'];
        $code = $validated['code'];

        // Utiliser le service pour vérifier le code
        $result = $this->loginCodeService->verifyLoginCode($email, $code);

        if (!$result['success']) {
            return $this->error($result['message'], 401);
        }

        // Code valide : récupérer l'utilisateur et créer un token
        $user = User::where('email', $email)->first();
        $tokenModel = $user->createToken('api-token');
        $token = $tokenModel->plainTextToken;
        $expiration = config('sanctum.expiration');
        $expiresIn = $expiration ? ($expiration * 60) : null;

        Log::info('User logged in via login code', ['user_id' => $user->id, 'email' => $user->email]);

        return $this->success([
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $expiresIn,
            'user' => $user,
        ], 'Connexion réussie.');
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return $this->error('Not authenticated', 401);
        }

        if (method_exists($user, 'currentAccessToken')) {
            $user->currentAccessToken()?->delete();
        } else {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return $this->success(null, 'Logged out');
    }

    public function me(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return $this->error('Not authenticated', 401);
        }

        // Charger les assignments actifs avec leurs stocks
        $user->load([
            'stockAssignments' => function ($query) {
                $query->with(['stock']) // Charger le stock et le rôle
                    ->orderBy('updated_at', 'desc');
            }
        ]);
        $user->loadCount('assignedMovements', 'assignedMovementsValidated');
        Log::info($user);

        return $this->success(new UserResource($user));
    }
}
