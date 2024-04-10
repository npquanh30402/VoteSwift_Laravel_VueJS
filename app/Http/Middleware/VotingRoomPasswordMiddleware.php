<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class VotingRoomPasswordMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $room = $request->route('room');
            $user = Auth::user();

            // Allow owner to access
            if ($this->isRoomOwner($room, $user)) {
                return $next($request);
            }

            if ($room->settings->password === null) {
                return $next($request);
            }

            // Handle access via password QR code and token
            if ($this->canAccessWithToken($room, $request, $user)) {
                return $next($request);
            }

            // Handle access via session-stored password
            if ($this->canAccessWithSessionPassword($room, $request, $user)) {
                return $next($request);
            }

            // If none of the above, redirect to password form
            return $this->redirectToPasswordForm($room);
        } catch (Exception $e) {
            Log::error('Error in AccessVotingRoomMiddleware: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while processing your request.'], 500);
        }
    }

    private function isRoomOwner($room, $user)
    {
        return $user->id == $room->user_id;
    }

    private function canAccessWithToken($room, Request $request, $user)
    {
        if ($room->settings->password_qrcode !== null && isset($request->token)) {
            $tokenCacheKey = "room{$room->id}_pwl.tkn.{$request->token}";
            $passwordCache = Cache::get($tokenCacheKey);

            if ($room->settings->invitation_only && !$this->isUserInvited($room, $user)) {
                return false;
            }

            return Hash::check($passwordCache, $room->settings->password);
        }

        return false;
    }

    private function isUserInvited($room, $user)
    {
        return $room->invitations()->where('invited_user_id', $user->id)->exists();
    }

    private function canAccessWithSessionPassword($room, Request $request, $user)
    {
        $session_name = "{$user->id}_voting_room_password_{$room->id}";

        return $room->settings->password !== null && $request->session()->get($session_name);
    }

    private function redirectToPasswordForm($room)
    {
        return redirect()->route('vote.password.form', ['room' => $room]);
    }
}
