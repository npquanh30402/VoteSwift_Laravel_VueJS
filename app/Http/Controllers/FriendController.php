<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\FriendRequestAccepted;
use App\Notifications\FriendRequestSend;
use App\Services\FriendService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FriendController extends Controller
{
    protected $friendService;

    public function __construct(FriendService $friendService)
    {
        $this->friendService = $friendService;
    }

    public function getFriends()
    {
        $userData = $this->friendService->getFriends(auth()->user());

        return Inertia::render('Users/FriendList', compact('userData'));
    }

    public function unfriend(User $friend)
    {
        $this->friendService->unfriend(auth()->user(), $friend);

        return back()->with('success', 'Friend removed successfully!');
    }

    public function abortRequestSent(User $recipient)
    {
        $this->friendService->abortRequestSent(auth()->user(), $recipient);

        return back()->with('success', 'Friend request aborted!');
    }

    public function acceptFriendRequest(User $sender)
    {
        $authUser = Auth::user();

        $this->friendService->acceptFriendRequest($authUser, $sender);

        $sender->notify(new FriendRequestAccepted($authUser));

        return back()->with('success', 'Friend request accepted!');
    }

    public function rejectFriendRequest(User $sender)
    {
        $authUser = Auth::user();

        $this->friendService->rejectFriendRequest($authUser, $sender);

        return back()->with('success', 'Friend request rejected!');
    }

    public function sendFriendRequest(User $recipient)
    {
        $authUser = Auth::user();
        $data = $this->friendService->sendFriendRequest($authUser, $recipient);

        $recipient->notify(new FriendRequestSend($authUser));

        return back()->with($data['type'], $data['message']);
    }
}
