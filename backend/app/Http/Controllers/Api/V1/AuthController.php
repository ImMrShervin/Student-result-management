<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(['email' => __('auth.failed')]);
        }
        if (! $user->is_active) {
            throw ValidationException::withMessages(['email' => __('Your account is disabled.')]);
        }
        $user->forceFill(['last_login_at' => now()])->save();
        $token = $user->createToken($request->userAgent() ?? 'api', ['*'], now()->addDays(30));

        return response()->json([
            'token' => $token->plainTextToken,
            'user' => new UserResource($user->load(['roles', 'student', 'teacher'])),
        ]);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create($request->validated() + ['is_active' => true]);
        $user->assignRole('student');
        $token = $user->createToken('registration', ['*'], now()->addDays(30));

        return response()->json([
            'token' => $token->plainTextToken,
            'user' => new UserResource($user->load(['roles', 'student', 'teacher'])),
        ], 201);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    public function me(Request $request): UserResource
    {
        return new UserResource($request->user()->load(['roles', 'student.department.faculty', 'teacher.department']));
    }

    public function forgot(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);
        $status = Password::sendResetLink($request->only('email'));
        return response()->json(['status' => __($status)]);
    }

    public function reset(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);
        $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $u, string $pass) { $u->forceFill(['password' => Hash::make($pass)])->save(); }
        );
        return response()->json(['status' => __($status)]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|min:8|confirmed',
        ]);
        $request->user()->forceFill(['password' => Hash::make($request->password)])->save();
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Password changed. Please log in again.']);
    }
}
