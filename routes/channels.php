<?php

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

Broadcast::channel('voting', function () {
    return true;
});
