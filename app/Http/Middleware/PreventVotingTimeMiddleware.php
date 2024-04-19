<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventVotingTimeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $room = $request->route('room');
        
        if ($room->is_published === 0) {
            return redirect()->route('homepage')->with('error', 'Voting room is not published.');
        }

        if (Carbon::now()->lessThan(Carbon::parse($room->start_time))) {
            return redirect()->route('homepage')->with('error', 'Voting has not started for this room yet.');
        }

        if (Carbon::now()->greaterThan(Carbon::parse($room->end_time))) {
            return redirect()->route('homepage')->with('error', 'Voting has ended for this room.');
        }

        return $next($request);
    }
}
