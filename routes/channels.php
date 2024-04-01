<?php

use App\Models\User;
use App\Models\VotingRoom;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int)$user->id === (int)$id;
});

Broadcast::channel('status-notifications', function ($user) {
    return true;
});

Broadcast::channel('result-update', function ($user) {
    return true;
});

Broadcast::channel('chat', function ($user) {
    if ($user != null) {
        return ['id' => $user->id, 'name' => $user->username];
    }
});

Broadcast::channel('chat.{user}', function ($user) {
    if ($user != null) {
        return ['id' => $user->id, 'name' => $user->username];
    }
});

Broadcast::channel('voting.{room}', function (User $user, VotingRoom $room) {
    if ($room && $room->userHasAccess($user)) {
        return [
            'id' => $user->id,
            'username' => $user->username,
            'avatar' => $user->avatar,
        ];
    }

    return false;
});
