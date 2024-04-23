<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Models\UserSetting;
use App\Services\UserService;
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

    public function profile(User $user)
    {
        $public_rooms = $user->getPublicRooms();

        $public_rooms->map(function ($room) {
            $room->room_name = Crypt::decryptString($room->room_name);
        });

        return Inertia::render('Users/UserProfile', [
            'user' => $user->decryptUser(),
            'public_rooms' => $public_rooms
        ]);
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

    public function storeInformation(UserRequest $request)
    {
        $user = auth()->user();

        $this->userService->updateInformation($user, $request->validated());
    }

    public function getDashboard()
    {
        return Inertia::render('Users/Dashboard');
    }
}
