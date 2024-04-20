<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthService
{
    /**
     * @throws Exception
     */
    public function emailVerification(EmailVerificationRequest $request): void
    {
        try {
            $request->fulfill();
        } catch (Exception $e) {
            Log::debug('Error during email verification: ' . $e->getMessage());
            throw $e;
        }
    }

    public function userLogin($requestData): ?bool
    {
        try {
            $credentials = $requestData->only('username', 'password');
            $remember = $requestData->remember_me;

            if (Auth::attempt($credentials, $remember)) {
                $requestData->session()->regenerate();

                return true;
            }

            return false;
        } catch (Exception $e) {
            Log::debug('Login error: ' . $e->getMessage());
            return false;
        }
    }

    public function userRegister($requestData): bool|User
    {
        try {
            $user = new User();
            $user->username = $requestData['username'];
            $user->email = $requestData['email'];
            $user->password = Hash::make($requestData['password']);

            $user->save();

            return $user;
        } catch (Exception $e) {
            Log::debug('Error during user registration: ' . $e->getMessage());
            return false;
        }
    }

    public function userLogout($requestData): void
    {
        try {
            Auth::logout();
            $requestData->session()->invalidate();
            $requestData->session()->regenerateToken();
        } catch (Exception $e) {
            Log::debug('Error during logout: ' . $e->getMessage());
            throw $e;
        }
    }
}
