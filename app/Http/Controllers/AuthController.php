<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Services\UserService;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use RuntimeException;

class AuthController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function getVerifyPage()
    {
        return Inertia::render('Users/Auth/VerifyEmail');
    }

    public function verifyEmail(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return redirect()->route('dashboard.user');
    }

    public function sendEmailVerificationNotification(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
    }

    public function logout(Request $request)
    {
        try {
            $this->userService->logout($request);
            return redirect()->route('homepage');
        } catch (Exception $e) {
            return back()->withErrors([
                'logout' => $e->getMessage()
            ]);
        }
    }

    public function register(UserRequest $request)
    {
        try {
            $user = $this->userService->register($request);

            if (!$user) {
                throw new RuntimeException("Registration failed.");
            }

            auth()->login($user);

            return redirect()->route('homepage');
        } catch (Exception $e) {
            return back()->withErrors([
                'register' => $e->getMessage()
            ]);
        }
    }

    public function login(UserRequest $request)
    {
        try {
            if ($this->userService->login($request)) {
                return redirect()->intended();
            }
            throw new RuntimeException("Login failed.");
        } catch (Exception $e) {
            return back()->withErrors([
                'login' => $e->getMessage()
            ]);
        }

    }

    public function getRegistrationForm()
    {
        return Inertia::render('Users/Auth/Register');
    }

    public function getLoginForm()
    {
        return Inertia::render('Users/Auth/Login');
    }
}
