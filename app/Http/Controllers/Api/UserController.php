<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponses;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use App\Exceptions\BusinessException;
use App\Http\Resources\StockMovementResource;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    use ApiResponses, AuthorizesRequests;

    public function __construct(protected UserService $service) {}

    public function store(StoreUserRequest $request)
    {
        // authorize performed in FormRequest
        try {
            $user = $this->service->create($request->validated());

            return $this->created(new UserResource($user));
        } catch (BusinessException $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', \App\Models\User::class);

        $filters = $request->only(['search', 'role', 'page']);
        $users = $this->service->list($filters);

        return $this->success([
            'items' => UserResource::collection($users),
            'meta' => [
                'total' => User::count(),
                'admins' => User::where('is_admin', true)->count(),
                'managers' => User::where('is_manager', true)->count(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
            ]
        ]);
    }

    public function show(User $user)
    {
        $this->authorize('create', \App\Models\User::class);

        if (! $user) {
            return $this->error('User not found', 404);
        }

        return $this->success(new UserResource($user));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);
        $user = $this->service->find($user->id);
        if (! $user) {
            return $this->error('User not found', 404);
        }
        $this->service->update($user, $request->all());
        // Update logic to be implemented
        return $this->success(new UserResource($user));
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user = $this->service->find($user->id);
        if (! $user) {
            return $this->error('User not found', 404);
        }

        $this->service->disable($user);

        return $this->success(null, 'User disabled');
    }

    public function toggleManagerRole(User $user)
    {
        // Vérifie que l’utilisateur connecté peut modifier CET utilisateur
        $this->authorize('update', $user);

        $user = $this->service->find($user->id);
        if (! $user) {
            return $this->error('User not found', 404);
        }

        $user->is_manager = ! $user->is_manager;
        $user->save();

        return $this->success(new UserResource($user), 'User role updated');
    }

    public function allManager()
    {
        $managers = User::where('is_manager', true)->get();

        return $this->success(
            $managers,
            'Tous les managers'
        );
    }

    public function updateAvatar(Request $request)
    {
        // 1. Validation rigoureuse
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = $request->user();

        // 2. Utilisation de Spatie pour stocker le média
        // La méthode 'addMediaFromRequest' cherche la clé 'avatar' dans ton FormData
        $user->addMediaFromRequest('avatar')
            ->toMediaCollection('avatars');

        // 3. Retourner le profil mis à jour via le Resource
        return $this->success(
            new UserResource($user),
            'Avatar mis à jour avec succès',
        );
    }

    public function mouvementAssignedToUserConnected(Request $request)
    {
        $filters = $request->only([
            'status',
            'start_date',
            'end_date',
            'search', // Le texte saisi par l'utilisateur
            'beneficiary_email'
        ]);
        Log::info($request);

        $query = StockMovement::query();

        // 1. Filtrer par Email du bénéficiaire
        $query->when($filters['beneficiary_email'] ?? null, function ($q, $email) {
            return $q->where('beneficiary_email', $email);
        });

        // 2. NOUVEAU : Recherche globale (Produit, Catégorie, Unité, Stock)
        $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->where(function ($subQuery) use ($search) {
                $subQuery->whereHas('stockProduct', function ($stockProdQuery) use ($search) {
                    $stockProdQuery->whereHas('product', function ($prodQuery) use ($search) {
                        $prodQuery->where('name', 'LIKE', "%{$search}%")
                            ->orWhereHas('category', function ($catQuery) use ($search) {
                                $catQuery->where('name', 'LIKE', "%{$search}%");
                            })->orWhereHas('unit', function ($catQuery) use ($search) {
                                $catQuery->where('name', 'LIKE', "%{$search}%");
                            });
                    })
                        ->orWhereHas('stock', function ($sQuery) use ($search) {
                            $sQuery->where('name', 'LIKE', "%{$search}%");
                        });
                });
            });
        });

        // 3. Filtrer par Type
        $query->when($filters['status'] ?? null, function ($q, $status) {
            $status = strtolower($status); // On passe tout en minuscules pour la comparaison

            if ($status === 'pending') {
                return $q->whereNull('validated_at');
            }

            if ($status === 'validated') {
                return $q->whereNotNull('validated_at');
            }
        });

        // 4. Filtrer par Dates
        $query->when($filters['start_date'] ?? null, function ($q, $start) {
            return $q->whereDate('created_at', '>=', $start);
        });

        $query->when($filters['end_date'] ?? null, function ($q, $end) {
            return $q->whereDate('created_at', '<=', $end);
        });

        // On charge aussi 'stockProduct.stock' pour que l'utilisateur voit d'où vient le produit
        $movements = $query->with(['stockProduct.product', 'stockProduct.stock', 'creator'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        Log::info($movements[0] ?? 'Aucun mouvement trouvé');

        return StockMovementResource::collection($movements)
            ->response()
            ->getData(true);
    }
}
