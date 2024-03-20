<?php

namespace App\Http\Middleware;

use App\Models\VotingRoom;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VotingRoomPasswordMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $room = $request->route('room');

        $votingRoom = VotingRoom::findOrFail($room->id);

        $user = Auth::user();
        $session_name = "{$user->id}_voting_room_password_{$votingRoom->id}";

        if ($user->id == $votingRoom->user_id) {
            return $next($request);
        }

        if ($votingRoom->settings->password != null && !$request->session()->get($session_name)) {
            return redirect()->route('vote.password.form', ['room' => $votingRoom]);
        }

        return $next($request);
    }
}
