<?php

namespace App\Http\Middleware;

use App\Models\VotingRoom;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventVotingAfterEndMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $room = $request->route('room');
        
        if (Carbon::now()->greaterThan(Carbon::parse($room->end_time))) {
            $room->endVote();

            return redirect()->route('homepage')->with('error', 'Voting has ended for this room.');
        }

        return $next($request);
    }
}
