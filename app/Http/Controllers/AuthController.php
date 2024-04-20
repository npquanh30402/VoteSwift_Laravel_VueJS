<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\AuthService;
use App\Services\NotificationService;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
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

    public function showForgotPasswordForm()
    {
        return Inertia::render('Users/Auth/ForgotPassword');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Password reset link sent successfully'
            ]);
        }

        return response()->json([
            'message' => trans($status)
        ], 422);
    }

    public function showResetPasswordForm(Request $request, string $token)
    {
        $email = $request->query('email');

        return Inertia::render('Users/Auth/ResetPassword', compact('token', 'email'));
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Password reset successfully')
            : back()->withErrors(['email' => [__($status)]]);
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

            $user->sendEmailVerificationNotification();

            auth()->login($user);

            return redirect()->route('homepage');
        } catch (Exception $e) {
            return back()->withErrors([
                'message' => 'Registration failed: ' . $e->getMessage()
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
