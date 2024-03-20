<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\UserService;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Inertia\Inertia;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function profile(User $user)
    {
        $public_rooms = $user->getPublicRooms();

        $public_rooms->map(function ($room) {
            $room->room_name = Crypt::decryptString($room->room_name);
        });

        return view('users.profile', compact('user', 'public_rooms'));
    }

    public function showInformation()
    {
        $user = auth()->user();

        return view('users.information', compact('user'));
    }

    public function storeInformation(UserRequest $request)
    {
        $user = auth()->user();

        $this->userService->updateInformation($user, $request->validated());

        return back()->with('success', 'Update information successfully!');
    }

    public function getDashboard()
    {
        $timezones = DateTimeZone::listIdentifiers();
        $timezones_with_offset = [];

        foreach ($timezones as $timezone) {
            $datetime = new DateTime('now', new DateTimeZone($timezone));
            $offset = $datetime->getOffset() / 3600;
            $offset_formatted = ($offset >= 0 ? '+' : '') . $offset;
            $timezones_with_offset[$timezone] = $offset_formatted;
        }

        asort($timezones_with_offset);

        $rooms = auth()->user()->rooms()->latest()->paginate(10);

        $rooms->getCollection()->transform(function ($room) {
            $room->room_name = Crypt::decryptString(strip_tags($room->room_name));
            $room->room_description = Crypt::decryptString(strip_tags($room->room_description));
            return $room;
        });

        return view('dashboard.user', compact('rooms', 'timezones_with_offset'));
    }

    public function logout(Request $request)
    {
        $this->userService->logout($request);

        return redirect()->route('homepage')->with('success', 'Logout successfully!');
    }

    public function register(UserRequest $request)
    {
        auth()->login($this->userService->register($request));

        return redirect()->route('homepage')->with('success', 'Registration successfully!');
    }

    public function login(UserRequest $request)
    {
        if ($this->userService->login($request)) {
            return redirect()->route('dashboard.user')->with('success', 'Login successfully!');
        }

        return back()->with('error', 'Login failed!');
    }

    public function getRegisterForm()
    {
        return Inertia::render('Users/Register');
    }

    public function getLoginForm()
    {
        return Inertia::render('Users/Login');
    }
}
