<?php

namespace App\Http\Controllers\Api;

use App\Enums\BroadcastType;
use App\Events\FriendRequestEvent;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\FriendRequestAccepted;
use App\Notifications\FriendRequestSend;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class FriendController extends Controller
{
//    protected $friendService;
//
//    public function __construct(FriendService $friendService)
//    {
//        $this->friendService = $friendService;
//    }

    public function index()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                throw new RuntimeException('User not found');
            }

            $friends = $user->acceptedFriendsFrom()->get()->map(function ($user) {
                return $user->decryptUser();
            })->merge($user->acceptedFriendsTo()->get()->map(function ($user) {
                return $user->decryptUser();
            }));

            $friendRequests = $user->pendingFriendsFrom()->wherePivot('accepted', false)->get()->map(function ($user) {
                return $user->decryptUser();
            });

            $requestSent = $user->pendingFriendsTo()->get()->map(function ($user) {
                return $user->decryptUser();
            });

            $friendsCollection = compact('friends', 'friendRequests', 'requestSent');

            return response()->json([
                'data' => $friendsCollection,
                'message' => 'Friends fetched successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function unfriend(User $sender, User $receiver)
    {
        DB::beginTransaction();
        try {
            $sender->friendsTo()->detach($receiver);
            $sender->friendsFrom()->detach($receiver);

            DB::commit();

            broadcast(new FriendRequestEvent(sender: $sender, receiver: $receiver, broadcastType: BroadcastType::UNFRIEND))->toOthers();

            return response()->json([
                'message' => 'Unfriended successfully',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function abortRequestSent(User $sender, User $receiver)
    {
        DB::beginTransaction();
        try {
            $sender->pendingFriendsTo()->detach($receiver);

            DB::commit();

            broadcast(new FriendRequestEvent(sender: $sender, receiver: $receiver, broadcastType: BroadcastType::FRIEND_REQUEST_ABORTED))->toOthers();

            return response()->json([
                'message' => 'Request aborted successfully',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function acceptFriendRequest(User $sender, User $receiver)
    {
        DB::beginTransaction();
        try {
            $sender->pendingFriendsFrom()->updateExistingPivot($receiver, ['accepted' => true]);

            DB::commit();

            $receiver->notify(new FriendRequestAccepted($sender));

            broadcast(new FriendRequestEvent(sender: $sender, receiver: $receiver, broadcastType: BroadcastType::FRIEND_REQUEST_ACCEPTED))->toOthers();

            return response()->json([
                'data' => $receiver->decryptUser(),
                'message' => 'Friend request accepted successfully',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function rejectFriendRequest(User $sender, User $receiver)
    {
        DB::beginTransaction();
        try {
            $sender->pendingFriendsFrom()->detach($receiver);

            DB::commit();

            broadcast(new FriendRequestEvent(sender: $sender, receiver: $receiver, broadcastType: BroadcastType::FRIEND_REQUEST_REJECTED))->toOthers();

            return response()->json([
                'data' => $sender->decryptUser(),
                'message' => 'Friend request rejected successfully',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function sendFriendRequest(User $sender, User $receiver)
    {
        DB::beginTransaction();
        try {
            if ($receiver->id === $sender->id) {
                throw new RuntimeException("You cannot send a friend request to yourself!");
            }

            if ($sender->acceptedFriendsTo()->where('friend_id', $receiver->id)->exists()) {
                throw new RuntimeException("Already friends!");
            }

            if (!$sender->friendsTo()->where('friend_id', $receiver->id)->exists()) {
                $sender->friendsTo()->attach($receiver, ['accepted' => false]);
            }

            DB::commit();

            $receiver->notify(new FriendRequestSend($sender));

            broadcast(new FriendRequestEvent(sender: $sender, receiver: $receiver, broadcastType: BroadcastType::FRIEND_REQUEST_SENT))->toOthers();

            return response()->json([
                'data' => $receiver->decryptUser(),
                'message' => 'Friend request sent successfully',
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
