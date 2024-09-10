<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Mail\ChangePassword;
use App\Mail\MailNotify;
use App\Models\PasswordReset;
use App\Models\User;
use App\Models\UserCode;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthService
{
    public function register(array $data): JsonResponse
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $user->createToken('auth_token')->plainTextToken;

        $code = Str::random(32);
        UserCode::query()->create([
            'code' => $code,
            'user_id' => $user->id,
        ]);

        Mail::to($user->email)->send(new MailNotify($code));

        return response()->json(['message' => 'Code sent successfully']);
    }

    public function login(array $data): string
    {
        if (!Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            throw new \Exception('Invalid login details');
        }

        $user = User::where('email', $data['email'])->firstOrFail();

        if ($user->email_verified_at === null) {
            throw new \Exception('Email not verified');
        }

        return $user->createToken('auth_token')->plainTextToken;
    }

    public function logout(): JsonResponse
    {
        Auth::user()->tokens()->delete();

        return response()->json(['message' => 'Logged out']);
    }

    public function verify(array $data): JsonResponse
    {
        $trueCode = UserCode::where('code', $data['code'])->first();

        if (!$trueCode) {
            return response()->json(['message' => 'This code not found or invalid']);
        }

        $user = User::find($trueCode->user_id);

        $user->email_verified_at = now();
        $user->save();

        $trueCode->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['access_token' => $token, 'token_type' => 'Bearer']);
    }

    public function sendResetLink(array $data): JsonResponse
    {
        $token = Str::random(60);

        PasswordReset::updateOrCreate(
            ['email' => $data['email']],
            [
                'email' => $data['email'],
                'token' => $token,
                'created_at' => Carbon::now(),
            ]
        );

        Mail::to($data['email'])->send(new ChangePassword($token));

        return response()->json(['message' => 'Reset link sent successfully']);
    }

    public function resetPassword(array $data): JsonResponse
    {
        $reset = PasswordReset::where('token', $data['token'])->first();

        if (!$reset) {
            return response()->json(['message' => 'Invalid token']);
        }

        $user = User::where('email', $reset->email)->firstOrFail();
        $user->password = bcrypt($data['password']);
        $user->save();

        $reset->delete();

        return response()->json(['message' => 'Password changed successfully']);
    }
}
