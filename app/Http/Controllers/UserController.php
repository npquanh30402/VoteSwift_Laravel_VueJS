<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Models\UserSetting;
use App\Services\HelperService;
use App\Services\UserService;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Inertia\Inertia;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function showMusicPlayerSettings()
    {
        $music = Auth::user()->music;

        return Inertia::render('Users/MusicPlayerSettings', compact('music'));
    }

    public function updateMusicPlayerSettings(Request $request)
    {
        $authUser = Auth::user();

        if (!$authUser->settings) {
            $authUser->settings = new UserSetting();
        }

        $authUser->settings->user_id = $authUser->id;
        $authUser->settings->music_player_enabled = $request->isMusicPlayerEnable;

        $authUser->settings->save();
    }

    public function uploadMusic(Request $request)
    {
        $authUser = Auth::user();

        if ($request->hasFile('music')) {
            $music = $request->music;
            $fileName = $authUser->id . '-' . uniqid('', true) . '.' . $music->getClientOriginalExtension();

            $fileName = HelperService::sanitizeFileName($fileName);

            $oriFileName = $music->getClientOriginalName();

            $request->music->storeAs('uploads/music', $fileName, 'public');

            $authUser->music()->create([
                'user_id' => $authUser->id,
                'title' => $oriFileName,
                'url' => $fileName,
            ]);
        }

        return back()->with('success', 'Upload music successfully!');
    }

    public function profile(User $user)
    {
        $public_rooms = $user->getPublicRooms();

        $public_rooms->map(function ($room) {
            $room->room_name = Crypt::decryptString($room->room_name);
        });

        return Inertia::render('Users/UserProfile', [
            'user' => $user,
            'public_rooms' => $public_rooms
        ]);
    }

    public function showSettings()
    {
        return Inertia::render('Users/UserSettings');
    }

    public function storeInformation(UserRequest $request)
    {
        $user = auth()->user();

        $this->userService->updateInformation($user, $request->validated());

        return back()->with('success', 'Update information successfully!');
    }

    public function getDashboard()
    {
//        $rooms = auth()->user()->rooms()->latest()->paginate(10);
//        $rooms->getCollection()->transform(function ($room) {
//            $room->room_name = Crypt::decryptString(strip_tags($room->room_name));
//            $room->room_description = Crypt::decryptString(strip_tags($room->room_description));
//            return $room;
//        });

        $rooms = auth()->user()->rooms()->get()->transform(function ($room) {
            $room->room_name = Crypt::decryptString(strip_tags($room->room_name));
            $room->room_description = Crypt::decryptString(strip_tags($room->room_description));
            return $room;
        });

        return Inertia::render('Users/Dashboard', [
            'rooms' => $rooms,
        ]);
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
