<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegistrationRequest;
use App\Http\Resources\UserLoginResource;
use App\Http\Resources\UserRegistrationResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(UserRegistrationRequest $request): UserRegistrationResource
    {
        $validatedData = $request->validated();
        $user = $this->createNewUser($validatedData);
        $token = JWTAuth::fromUser($user);

        return (new UserRegistrationResource($user))->additional([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->guard('api')->factory()->getTTL() * 60,
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function login(UserLoginRequest $request): UserLoginResource
    {
        $validatedData = $request->validated();
        $token = auth()->attempt($validatedData);

        if (!$token) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = $this->fetchUser($validatedData['email']);

        if (!Hash::check($validatedData['password'], $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['The provided password is incorrect.'],
            ]);
        }

        return (new UserLoginResource([
            'message' => 'User successfully logged in.',
            'auth_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->guard('api')->factory()->getTTL() * 60,
        ]));
    }

    /**
     * @param array $validatedData
     * @return User
     */
    public function createNewUser(array $validatedData): User
    {
        return User::query()->create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);
    }

    /**
     * @param $email
     * @return User
     */
    public function fetchUser($email): User
    {
        return User::query()
            ->where('email', $email)
            ->firstOrFail();
    }

    public function logout(Request $request): JsonResponse
    {
        auth()->logout();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function me(): JsonResponse
    {
        return response()->json(auth()->user());
    }
}
