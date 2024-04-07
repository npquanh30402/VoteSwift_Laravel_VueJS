<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Models\UserSetting;
use App\Services\FriendService;
use App\Services\HelperService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Inertia\Inertia;

class UserController extends Controller
{
    protected $friendService;

    public function __construct(FriendService $friendService, UserService $userService)
    {
        $this->friendService = $friendService;
        $this->userService = $userService;
    }

    protected $userService;

    public function search(Request $request)
    {
        $query = $request->input('query');

        $users = User::where('id', $query)
            ->orWhere('username', 'like', "%$query%")
            ->orWhere('email', 'like', "%$query%")
            ->get();

        return response()->json($users);
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

    public function profile(User $user)
    {
        $public_rooms = $user->getPublicRooms();

        $public_rooms->map(function ($room) {
            $room->room_name = Crypt::decryptString($room->room_name);
        });

//        dd($user->decryptUser());

        return Inertia::render('Users/UserProfile', [
            'user' => $user->decryptUser(),
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
        $authUserFriends = $this->friendService->getFriends(auth()->user());

        return Inertia::render('Users/Dashboard', compact('authUserFriends'));
    }
}
