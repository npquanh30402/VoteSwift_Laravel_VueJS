<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFriendship
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $receiver = $request->route('user');

        $sender = $request->user();

        $areFriends = $sender->friendsFrom()->where('friends.accepted', true)->where('users.id', $receiver->id)->exists() ||
            $sender->friendsTo()->where('friends.accepted', true)->where('users.id', $receiver->id)->exists();

        if ($areFriends) {
            return $next($request);
        }

        return back()->with('error', 'You are not friends with this user.');
    }
}
