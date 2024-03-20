<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VotingRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_name',
        'room_description',
        'start_time',
        'end_time',
        'timezone',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'voting_room_id', 'id');
    }

    public function settings()
    {
        return $this->hasOne(VotingRoomSetting::class, 'voting_room_id', 'id');
    }

    public function getPublicRooms()
    {
        return $this->with('settings')->whereHas('settings', function ($query) {
            $query->where('public_visibility', true);
        })->get();
    }
}
