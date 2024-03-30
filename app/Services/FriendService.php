<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

class FriendService
{
    public function getFriends(User $user)
    {
        try {
            $friends = $user->acceptedFriendsFrom->map(function ($user) {
                return $user->decryptUser();
            })->merge($user->acceptedFriendsTo->map(function ($user) {
                return $user->decryptUser();
            }));

            $friendRequests = $user->pendingFriendsFrom()->wherePivot('accepted', false)->get()->map(function ($user) {
                return $user->decryptUser();
            });

            $requestSent = $user->pendingFriendsTo()->get()->map(function ($user) {
                return $user->decryptUser();
            });

            return compact('friends', 'friendRequests', 'requestSent');
        } catch (Exception $e) {
            Log::debug('Error in getFriends function: ' . $e->getMessage());
        }
    }

    public function unfriend(User $user, User $friend)
    {
        try {
            $user->friendsTo()->detach($friend);
            $user->friendsFrom()->detach($friend);
        } catch (Exception $e) {
            Log::debug('An error occurred while unfriending: ' . $e->getMessage());
        }
    }

    public function abortRequestSent(User $user, User $recipient)
    {
        try {
            $user->pendingFriendsTo()->detach($recipient);
        } catch (Exception $e) {
            Log::debug('An error occurred while aborting friend request: ' . $e->getMessage());
        }
    }

    public function acceptFriendRequest(User $user, User $sender)
    {
        try {
            $user->pendingFriendsFrom()->updateExistingPivot($sender, ['accepted' => true]);
        } catch (Exception $e) {
            Log::debug('An error occurred while accepting friend request: ' . $e->getMessage());
        }
    }

    public function rejectFriendRequest(User $user, User $sender)
    {
        try {
            $user->pendingFriendsFrom()->detach($sender);
        } catch (Exception $e) {
            Log::debug('An error occurred while rejecting friend request: ' . $e->getMessage());
        }
    }

    public function sendFriendRequest(User $sender, User $recipient)
    {
        $type = "error";
        $message = "Friend request already sent!";

        try {
            if ($recipient->id == $sender->id) {
                $message = "You cannot send a friend request to yourself!";
            }

            if (!$sender->friendsTo()->where('friend_id', $recipient->id)->exists()) {
                $sender->friendsTo()->attach($recipient, ['accepted' => false]);
                $type = "success";
                $message = "Friend request sent!";
            }

            if ($sender->acceptedFriendsTo()->where('friend_id', $recipient->id)->exists()) {
                $message = "Already friends!";
            }

            return compact('type', 'message');
        } catch (Exception $e) {
            Log::debug('An error occurred while sending friend request: ' . $e->getMessage());
            return null;
        }
    }
}
