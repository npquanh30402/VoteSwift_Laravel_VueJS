<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\FriendRequestAccepted;
use App\Notifications\FriendRequestSend;
use App\Services\FriendService;
use Illuminate\Support\Facades\Auth;

class FriendController extends Controller
{
    protected $friendService;

    public function __construct(FriendService $friendService)
    {
        $this->friendService = $friendService;
    }

    function getFriends()
    {
        $friendsCollection = $this->friendService->getFriends(Auth::user());

        return response()->json($friendsCollection);
    }

    public function unfriend(User $friend)
    {
        $this->friendService->unfriend(Auth::user(), $friend);

        return response()->json(['success' => 'Friend removed successfully!']);
    }

    public function abortRequestSent(User $recipient)
    {
        $this->friendService->abortRequestSent(Auth::user(), $recipient);

        return response()->json(['success' => 'Friend request aborted!']);
    }

    public function acceptFriendRequest(User $sender)
    {
        $this->friendService->acceptFriendRequest(Auth::user(), $sender);

        $sender->notify(new FriendRequestAccepted(Auth::user()));

        return response()->json(['success' => 'Friend request accepted!']);
    }

    public function rejectFriendRequest(User $sender)
    {
        $this->friendService->rejectFriendRequest(Auth::user(), $sender);

        return response()->json(['success' => 'Friend request rejected!']);
    }

    public function sendFriendRequest(User $recipient)
    {
        $data = $this->friendService->sendFriendRequest(Auth::user(), $recipient);

        $recipient->notify(new FriendRequestSend(Auth::user()));

        return response()->json($data);
    }
}
