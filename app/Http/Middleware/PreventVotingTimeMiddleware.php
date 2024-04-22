<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PreventVotingTimeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $room = $request->route('room');
        $user = Auth::user();

        if ($user->id == $room->user_id) {
            return $next($request);
        }

        if ($room->is_published === 0) {
            return redirect()->route('homepage')->with('error', 'Voting room is not published.');
        }

        $now = Carbon::now();

        if ($now->lt(Carbon::parse($room->start_time))) {
            return redirect()->route('homepage')->with('error', 'Voting has not started for this room yet.');
        }

        if ($now->gt(Carbon::parse($room->end_time))) {
            $room->endVote();
            
            return redirect()->route('homepage')->with('error', 'Voting has ended for this room.');
        }

        return $next($request);
    }
}
