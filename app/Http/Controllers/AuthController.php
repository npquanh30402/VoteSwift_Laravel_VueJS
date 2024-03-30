<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Services\UserService;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Inertia\Inertia;
use RuntimeException;

class AuthController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function logout(Request $request)
    {
        try {
            $this->userService->logout($request);
            return redirect()->route('homepage')->with('success', 'Logout successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Logout failed!');
        }
    }

    public function register(UserRequest $request)
    {
        try {
            $user = $this->userService->register($request);

            if (!$user) {
                throw new RuntimeException("User registration failed.");
            }

            auth()->login($user);

            event(new Registered($user));

            return redirect()->route('homepage')->with('success', 'Registration successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function login(UserRequest $request)
    {
        try {
            if ($this->userService->login($request)) {
                return redirect()->intended()->with('success', 'Login successfully!');
            }
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred during login.');
        }

        return back()->with('error', 'Login failed!');
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
