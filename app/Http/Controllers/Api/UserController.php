<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponses;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use App\Exceptions\BusinessException;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

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

    public function allMovement()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $count = $user->createdMovements()->count();

        return $this->success(
            $count,
            'Total des mouvements initiés par l’utilisateur connecté'
        );
    }
}
