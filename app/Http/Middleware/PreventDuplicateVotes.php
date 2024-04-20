<?php

namespace App\Http\Middleware;

use App\Models\Vote;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventDuplicateVotes
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = auth()->id();
        $room = $request->route('room');

        if ($userId == $room->user_id) {
            return $next($request);
        }

        $existingVote = Vote::where('user_id', $userId)
            ->where('voting_room_id', $room->id)
            ->exists();

        if ($existingVote) {
            return redirect()->route('homepage')->with('error', 'You have already voted in this room.');
        }

        return $next($request);
    }
}
