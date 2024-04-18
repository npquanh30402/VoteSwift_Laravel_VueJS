<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class VotingRoomAuthorizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Merge the token into the request
        $request->merge(['token' => $request->query('token')]);

        try {
            $room = $request->route('room');
            $user = Auth::user();
            $token = $request->query('token');

            if ($this->isRoomOwner($room, $user)) {
                return $next($request);
            }

            if ($this->canAccessRoom($room, $user, $token)) {
                return $next($request);
            }

            return redirect()->route('vote.password.form', ['room' => $room, 'token' => $token]);
        } catch (AuthorizationException $e) {
            return Inertia::render('Errors/Unauthorized');
        } catch (Exception $e) {
            Log::error('Error in VotingRoomAuthorizationMiddleware: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while processing your request.'], 500);
        }
    }

    private function isRoomOwner($room, $user)
    {
        return $user->id == $room->user_id;
    }

    /**
     * @throws AuthorizationException
     */
    private function canAccessRoom($room, $user, $token)
    {
        $settings = $room->settings()->first();

        if ($settings->password !== null && $settings->invitation_only === 1) {
            if (!isset($token)) {
                throw new AuthorizationException('Unauthorized access.');
            }

            if ($this->isUserInvited($room, $user, $token) && $this->checkCachePassword($room, $user)) {
                return true;
            }
        }

        if ($settings->password === null && $settings->invitation_only === 1) {
            if ($this->isUserInvited($room, $user, $token)) {
                return true;
            }

            throw new AuthorizationException('Unauthorized access.');
        }

        if ($settings->password !== null && $settings->invitation_only === 0) {
            if ($this->checkCachePassword($room, $user)) {
                return true;
            }
        } elseif ($settings->password === null && $settings->invitation_only === 0) {
            return true;
        }

        return false;
    }

    private function isUserInvited($room, $user, $token)
    {
        $tokenCacheKey = "ballot.tkn.{$token}";
        $userId = Cache::get($tokenCacheKey);
        return $userId === $user->id && $room->invitations()->where('invited_user_id', $userId)->exists();
    }

    private function checkCachePassword($room, $user)
    {
        $cacheKey = "{$user->id}_voting_room_password_{$room->id}";
        return Cache::get($cacheKey) !== null;
    }

    private function redirectToPasswordForm($room)
    {
        return redirect()->route('vote.password.form', ['room' => $room]);
    }

    private function checkPassword($room, $token)
    {
        $tokenCacheKey = "room{$room->id}_pwl.tkn.{$token}";
        $passwordCache = Cache::get($tokenCacheKey);

        return Hash::check($passwordCache, $room->settings->password);
    }
}
