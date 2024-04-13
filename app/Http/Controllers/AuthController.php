<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Services\AuthService;
use App\Services\NotificationService;
use Exception;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class AuthController extends Controller
{
    protected AuthService $authService;
    protected NotificationService $notificationService;

    public function __construct(AuthService $authService, NotificationService $notificationService)
    {
        $this->authService = $authService;
        $this->notificationService = $notificationService;
    }

    public function verifyEmail(EmailVerificationRequest $request): ?RedirectResponse
    {
        try {
            $this->authService->emailVerification($request);

            return redirect()->intended();
        } catch (Exception $e) {
            return back()->withErrors([
                'message' => 'Email verification failed: ' . $e->getMessage()
            ]);
        }
    }

    public function sendEmailVerificationNotification(Request $request): void
    {
        try {
            $this->notificationService->sendEmailVerificationNotification($request);
        } catch (Exception $e) {
            Log::debug('Error sending email verification notification: ' . $e->getMessage());
        }
    }

    public function logout(Request $request): ?RedirectResponse
    {
        try {
            $this->authService->userLogout($request);

            return redirect()->route('homepage');
        } catch (Exception $e) {
            return back()->withErrors([
                'message' => 'Logout failed: ' . $e->getMessage()
            ]);
        }
    }

    public function register(UserRequest $request): ?RedirectResponse
    {
        try {
            $user = $this->authService->userRegister($request);

            if (!$user) {
                throw new RuntimeException("Registration failed.");
            }

            auth()->login($user);

            return redirect()->route('homepage');
        } catch (Exception $e) {
            return back()->withErrors([
                'message' => 'Registration failed: ' . $e->getMessage()
            ]);
        }
    }

    public function login(UserRequest $request): ?RedirectResponse
    {
        try {
            if ($this->authService->userLogin($request)) {
                return redirect()->intended();
            }

            throw new RuntimeException("Login failed.");
        } catch (Exception $e) {
            return back()->withErrors([
                'message' => 'Login failed: ' . $e->getMessage()
            ]);
        }
    }

    public function getVerifyPage(): Response
    {
        return Inertia::render('Users/Auth/VerifyEmail');
    }

    public function getRegistrationForm(): Response
    {
        return Inertia::render('Users/Auth/Register');
    }

    public function getLoginForm(): Response
    {
        return Inertia::render('Users/Auth/Login');
    }
}
