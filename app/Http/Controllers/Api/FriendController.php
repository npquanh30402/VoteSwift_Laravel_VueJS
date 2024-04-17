<?php

namespace App\Http\Controllers\Api;

use App\Enums\BroadcastType;
use App\Events\FriendRequestEvent;
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

    public function unfriend(User $sender, User $receiver)
    {
        $this->friendService->unfriend($sender, $receiver);

        broadcast(new FriendRequestEvent(sender: $sender, receiver: $receiver, broadcastType: BroadcastType::UNFRIEND))->toOthers();

        return response()->json([
            'data' => null,
            'message' => 'Unfriended successfully',
        ]);
    }

    public function abortRequestSent(User $sender, User $receiver)
    {
        $this->friendService->abortRequestSent($sender, $receiver);

        broadcast(new FriendRequestEvent(sender: $sender, receiver: $receiver, broadcastType: BroadcastType::FRIEND_REQUEST_ABORTED))->toOthers();

        return response()->json([
            'data' => null,
            'message' => 'Request aborted successfully',
        ]);
    }

    public function acceptFriendRequest(User $sender, User $receiver)
    {
        $this->friendService->acceptFriendRequest($sender, $receiver);

        $receiver->notify(new FriendRequestAccepted($sender));

        broadcast(new FriendRequestEvent(sender: $sender, receiver: $receiver, broadcastType: BroadcastType::FRIEND_REQUEST_ACCEPTED))->toOthers();

        return response()->json([
            'data' => $receiver->decryptUser(),
            'message' => 'Friend request accepted successfully',
        ]);
    }

    public function rejectFriendRequest(User $sender, User $receiver)
    {
        $this->friendService->rejectFriendRequest($sender, $receiver);

        broadcast(new FriendRequestEvent(sender: $sender, receiver: $receiver, broadcastType: BroadcastType::FRIEND_REQUEST_REJECTED))->toOthers();

        return response()->json([
            'data' => $sender->decryptUser(),
            'message' => 'Friend request rejected successfully',
        ]);
    }

    public function sendFriendRequest(User $sender, User $receiver)
    {
        $this->friendService->sendFriendRequest($sender, $receiver);

        $receiver->notify(new FriendRequestSend($sender));

        broadcast(new FriendRequestEvent(sender: $sender, receiver: $receiver, broadcastType: BroadcastType::FRIEND_REQUEST_SENT))->toOthers();

        return response()->json([
            'data' => $receiver->decryptUser(),
            'message' => 'Friend request sent successfully',
        ]);
    }
}
