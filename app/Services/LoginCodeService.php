<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Mail\LoginCodeMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class LoginCodeService
{
    /**
     * Durée du code en minutes
     */
    private int $expirationMinutes = 10;

    /**
     * Nombre maximum de tentatives
     */
    private int $maxAttempts = 3;

    public function __construct()
    {
        $this->expirationMinutes = (int) config('auth.login_code_expiration', 10);
        $this->maxAttempts = (int) config('auth.login_code_max_attempts', 3);
    }

    /**
     * Générer et envoyer un code de connexion
     */
    public function sendLoginCode(string $email): bool
    {
        try {
            $user = User::where('email', $email)->first();

            if (!$user) {
                return false;
            }

            if (!$user->is_admin) {
                // Vérifier les assignments actives
                $activeAssignments = $user->stockAssignments()
                    ->active()
                    ->count();
                if ($activeAssignments === 0) {
                    // Vérifier s'il a des assignments expirées
                    $expiredAssignments = $user->stockAssignments()
                        ->where('ended_at', '<=', now())
                        ->count();

                    Log::warning('User has no active stock assignments', [
                        'user_id' => $user->id,
                        'active_assignments' => 0,
                        'expired_assignments' => $expiredAssignments,
                        'total_assignments' => $user->stockAssignments()->count(),
                    ]);

                    return false;
                }
            }

            // Générer un code à 6 chiffres
            $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Hasher le code
            $hashedCode = Hash::make($code);

            // Définir l'expiration
            $expirationTime = Carbon::now()->addMinutes($this->expirationMinutes);

            // Stocker le code hashé et l'expiration
            $user->update([
                'password' => $hashedCode,
                'login_code_expires_at' => $expirationTime,
                'login_attempts' => 0,
            ]);

            Log::info('Sending email to', ['email' => $user->email]);

            // Envoyer l'email
            Mail::to($user->email)->send(new LoginCodeMail($user, $code, $this->expirationMinutes));

            Log::info('Login code sent', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error sending login code', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Vérifier le code de connexion
     *
     * @return array{success: bool, message: string, user?: User, attempts_remaining?: int}
     */
    public function verifyLoginCode(string $email, string $code): array
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Email ou code incorrect.',
            ];
        }

        // Vérifier l'expiration
        if (!$user->login_code_expires_at || $user->login_code_expires_at < Carbon::now()) {
            $this->resetLoginCode($user);
            Log::warning('Expired login code attempt', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return [
                'success' => false,
                'message' => 'Code expiré. Veuillez demander un nouveau code.',
            ];
        }

        // Vérifier le nombre de tentatives
        if ($user->login_attempts >= $this->maxAttempts) {
            $this->resetLoginCode($user);
            Log::warning('Too many login attempts', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return [
                'success' => false,
                'message' => 'Trop de tentatives échouées. Veuillez demander un nouveau code.',
            ];
        }

        // Vérifier le code
        if (!Hash::check($code, $user->password ?? '')) {
            $user->increment('login_attempts');
            $remainingAttempts = $this->maxAttempts - $user->login_attempts;

            Log::warning('Invalid login code attempt', [
                'user_id' => $user->id,
                'email' => $user->email,
                'attempts' => $user->login_attempts,
            ]);

            $message = $remainingAttempts > 0
                ? "Code incorrect. Il vous reste {$remainingAttempts} tentative(s)."
                : 'Trop de tentatives échouées. Veuillez demander un nouveau code.';

            return [
                'success' => false,
                'message' => $message,
                'attempts_remaining' => max(0, $remainingAttempts),
            ];
        }

        // Code valide
        $this->resetLoginCode($user);

        Log::info('Login code verified successfully', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        // Charger les assignments actifs avec leurs stocks
        $user->load([
            'stockAssignments' => function ($query) {
                $query->whereNull('ended_at')
                    ->orWhere('ended_at', '>', now())
                    ->with(['stock', 'role']) // Charger le stock et le rôle
                    ->orderBy('started_at', 'desc');

                // $query->where(function ($q) {
                //     $q->whereNull('ended_at')
                //         ->orWhere('ended_at', '>', now());
                // })
                //     ->with(['stock:id,name,location', 'role:id,role,description'])
                //     ->orderBy('started_at', 'desc');
            }
        ]);

        return [
            'success' => true,
            'message' => 'Code vérifié avec succès.',
            'user' => new UserResource($user),
        ];
    }

    /**
     * Réinitialiser le code de connexion
     */
    private function resetLoginCode(User $user): void
    {
        $user->update([
            'password' => null,
            'login_code_expires_at' => null,
            'login_attempts' => 0,
        ]);
    }
}
