<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

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

    public function attachments()
    {
        return $this->hasMany(VotingRoomFiles::class, 'voting_room_id', 'id');
    }

    public static function getPublicRooms()
    {
        return DB::table('voting_rooms')
            ->join('voting_room_settings', 'voting_room_settings.voting_room_id', '=', 'voting_rooms.id')
            ->where('public_visibility', 1);
    }

    public function decryptVotingRoom()
    {
        $this->room_name = Crypt::decryptString($this->room_name);
        $this->room_description = Crypt::decryptString($this->room_description);
    }
}
