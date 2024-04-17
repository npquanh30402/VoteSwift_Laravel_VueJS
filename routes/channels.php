<?php

use App\Models\User;
use App\Models\VotingRoom;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Cache;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int)$user->id === (int)$id;
});

Broadcast::channel('friend-request.{user}', function ($user) {
    return true;
});

Broadcast::channel('status-notifications', function ($user) {
    return true;
});

Broadcast::channel('result-update', function ($user) {
    return true;
});

Broadcast::channel('chat.{user}', function ($user) {
    return ['id' => $user->id, 'username' => $user->username, 'avatar' => $user->avatar];
});

Broadcast::channel('voting.process.{room}', function (User $user, VotingRoom $room) {
    if ($room) {
        $isInvitationOnly = $room->settings->invitation_only;

        if (!$isInvitationOnly || $room->userHasAccess($user)) {
            return [
                'id' => $user->id,
                'username' => $user->username,
                'avatar' => $user->avatar,
            ];
        }
    }

    return false;
});
