<?php

namespace App\Http\Controllers\Api;

use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\UserResponseDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $loginDto = LoginDTO::fromRequest($request->validated());

        $user = User::where('email', $loginDto->email)->first();

        if (!$user || !Hash::check($loginDto->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;
        $userDto = UserResponseDTO::fromModel($user);

        return response()->json([
            'user' => $userDto->toArray(),
            'token' => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            if ($request->user()) {
                $request->user()->currentAccessToken()->delete();
            }
        } catch (\Exception $e) {
        }

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me(Request $request): JsonResponse
    {
        $userDto = UserResponseDTO::fromModel($request->user());

        return response()->json(['data' => $userDto->toArray()]);
    }
}