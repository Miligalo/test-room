<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetLinkRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\VerifyRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;


final class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $token = $this->authService->register($request->validated());

        return response()->json(['access_token' => $token, 'token_type' => 'Bearer']);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $token = $this->authService->login($request->validated());

        return response()->json(['access_token' => $token, 'token_type' => 'Bearer']);
    }

    public function logout(): JsonResponse
    {
        return $this->authService->logout();
    }

    public function verify(VerifyRequest $request): JsonResponse
    {
        return $this->authService->verify($request->validated());
    }

    public function sendResetLink(ResetLinkRequest $request): JsonResponse
    {
        return $this->authService->sendResetLink($request->validated());
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        return $this->authService->resetPassword($request->validated());
    }
}
