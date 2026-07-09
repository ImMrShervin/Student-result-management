<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $r)
    {
        return UserResource::collection(
            User::with('roles')
                ->when($r->role, fn ($q, $v) => $q->whereHas('roles', fn ($qr) => $qr->where('name', $v)))
                ->when($r->q, fn ($q, $v) => $q->where(fn ($qq) => $qq->where('first_name', 'like', "%$v%")->orWhere('last_name', 'like', "%$v%")->orWhere('email', 'like', "%$v%")))
                ->orderByDesc('id')
                ->paginate($r->integer('per_page', 20))
        );
    }

    public function updateProfile(Request $r)
    {
        $data = $r->validate([
            'first_name' => 'sometimes|string|max:80',
            'last_name'  => 'sometimes|string|max:80',
            'phone'      => 'nullable|string|max:32',
            'address'    => 'nullable|string|max:255',
            'locale'     => 'sometimes|in:en,fa',
            'gender'     => 'nullable|in:male,female',
            'birth_date' => 'nullable|date',
        ]);
        $r->user()->update($data);
        return new UserResource($r->user()->fresh()->load('roles'));
    }

    public function uploadAvatar(Request $r)
    {
        $r->validate(['avatar' => 'required|image|max:2048']);
        $path = $r->file('avatar')->store('avatars', 'public');
        $r->user()->update(['avatar_path' => $path]);
        return new UserResource($r->user()->fresh());
    }

    public function toggleActive(User $user)
    {
        $this->authorize('viewAny', User::class);
        $user->update(['is_active' => ! $user->is_active]);
        return new UserResource($user);
    }

    public function assignRole(Request $r, User $user)
    {
        $r->validate(['role' => 'required|string|exists:roles,name']);
        $user->syncRoles([$r->role]);
        return new UserResource($user->load('roles'));
    }
}
