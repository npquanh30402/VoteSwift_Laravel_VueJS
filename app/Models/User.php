<?php

namespace App\Models;

use App\Notifications\QueuedVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    protected $table = 'users';

    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'first_name',
        'last_name',
        'birth_date',
        'gender',
        'country',
        'city',
        'zip_code',
        'phone',
        'address',
        'avatar'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function sendEmailVerificationNotification()
    {
        $this->notify(new QueuedVerifyEmail());
    }

    public function decryptUser()
    {
//        if ($this->first_name) {
//            $this->first_name = Crypt::decryptString($this->first_name);
//        }
//        if ($this->last_name) {
//            $this->last_name = Crypt::decryptString($this->last_name);
//        }
        if ($this->phone) {
            $this->phone = Crypt::decryptString($this->phone);
        }
        if ($this->address) {
            $this->address = Crypt::decryptString($this->address);
        }

        return $this;
    }

    public function music()
    {
        return $this->hasMany(Music::class, 'user_id', 'id');
    }

    public function settings()
    {
        return $this->hasOne(UserSetting::class, 'user_id', 'id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id', 'id');
    }

    public function userJoinTimes()
    {
        return $this->hasMany(UserAttendance::class, 'user_id', 'id');
    }

    public function getPublicRooms()
    {
        return $this->rooms()->whereHas('settings', function ($query) {
            $query->where('public_visibility', true);
        })->get();
    }

    public function rooms()
    {
        return $this->hasMany(VotingRoom::class, 'user_id', 'id');
    }

    public function hasVoted()
    {
        return $this->votes()->exists();
    }

    public function votes()
    {
        return $this->hasMany(Vote::class, 'user_id', 'id');
    }

    public function hasVotedForVotingRoom($votingRoomId)
    {
        $userVotes = $this->votes;

        foreach ($userVotes as $vote) {
            if ($vote->candidate->question->room->id == $votingRoomId) {
                return true;
            }
        }

        return false;
    }

    public function hasVotedForCandidate(Candidate $candidate)
    {
        return $this->votes()->where('candidate_id', $candidate->id)->exists();
    }

    public function pendingFriendsFrom()
    {
        return $this->friendsFrom()->where('friends.accepted', false);
    }

    public function friendsFrom()
    {
        return $this->belongsToMany(User::class, 'friends', 'friend_id', 'user_id')
            ->select('users.*', 'friends.accepted')
            ->withTimestamps();
    }

    public function pendingFriendsTo()
    {
        return $this->friendsTo()->where('friends.accepted', false);
    }

    public function friendsTo()
    {
        return $this->belongsToMany(User::class, 'friends', 'user_id', 'friend_id')
            ->select('users.*', 'friends.accepted')
            ->withTimestamps();
    }

    public function acceptedFriendsFrom()
    {
        return $this->friendsFrom()->where('friends.accepted', true);
    }

    public function acceptedFriendsTo()
    {
        return $this->friendsTo()->where('friends.accepted', true);
    }

    protected function avatar(): Attribute
    {
        return Attribute::make(fn($value) => $value ? asset('storage/images/avatars/' . $value) : '/fallback-avatar.jpg');
    }
}
