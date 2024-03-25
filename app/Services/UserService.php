<?php

namespace App\Services;

use App\Events\UserActivity;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserService
{
    public function updateInformation(User $user, $requestData)
    {
        try {
            $oldAvatar = $user->avatar;

            $user->first_name = $requestData['first_name'];
            $user->last_name = $requestData['last_name'];
            $user->phone = $requestData['phone'];
            $user->address = $requestData['address'];

            if (isset($requestData['avatar'])) {
                $fileName = $user->id . '-' . uniqid('', true) . '.' . $requestData['avatar']->getClientOriginalExtension();

                $fileName = HelperService::sanitizeFileName($fileName);

                $requestData['avatar']->storeAs('images/avatars', $fileName, 'public');
                $user->avatar = $fileName;
            }

            $user->save();

            if ($oldAvatar !== '/fallback-avatar.jpg' && $oldAvatar !== $user->avatar) {
                Storage::delete(str_replace('/storage/', 'public/', $oldAvatar));
            }
        } catch (Exception $e) {

            Log::debug('Error updating user information: ' . $e->getMessage());
        }
    }

    public function login($requestData)
    {
        try {
            $credentials = $requestData->only('username', 'password');
            $remember = $requestData->has('remember_me');

            if (Auth::attempt($credentials, $remember)) {
                $requestData->session()->regenerate();

                $friendIds = $this->getFriendIds($requestData->user());

                broadcast(new UserActivity($friendIds, "{$requestData->user()->username} is online", 'success'))->toOthers();

                return true;
            }

            return false;
        } catch (Exception $e) {
            Log::debug('Login error: ' . $e->getMessage());
            return false;
        }
    }

    public function register($requestData)
    {
        try {
            $user = new User();
            $user->username = $requestData['username'];
            $user->email = $requestData['email'];
            $user->password = bcrypt($requestData['password']);

            $user->save();

            return $user;
        } catch (Exception $e) {
            Log::debug('Error during user registration: ' . $e->getMessage());
            return null;
        }
    }

    public function logout($requestData)
    {
        try {
            $friendIds = $this->getFriendIds($requestData->user());

            broadcast(new UserActivity($friendIds, "{$requestData->user()->username} is offline", 'danger'))->toOthers();
            Auth::logout();
            $requestData->session()->invalidate();
            $requestData->session()->regenerateToken();

        } catch (Exception $e) {
            Log::debug('Error during logout: ' . $e->getMessage());
        }
    }

    public function getFriendIds(User $user)
    {
        return $user->acceptedFriendsFrom->merge($user->acceptedFriendsTo)->pluck('id')->toArray();
    }
}
