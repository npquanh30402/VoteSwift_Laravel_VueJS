<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\FriendService;

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

        return view('users.friends', compact('userData'));
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
        $this->friendService->acceptFriendRequest(auth()->user(), $sender);

        return back()->with('success', 'Friend request accepted!');
    }

    public function rejectFriendRequest(User $sender)
    {
        $this->friendService->rejectFriendRequest(auth()->user(), $sender);

        return back()->with('success', 'Friend request rejected!');
    }

    public function sendFriendRequest(User $recipient)
    {
        $data = $this->friendService->sendFriendRequest(auth()->user(), $recipient);
        
        return back()->with($data['type'], $data['message']);
    }
}
