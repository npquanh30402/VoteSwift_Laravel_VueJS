<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAttendance extends Model
{
    use HasFactory;

    protected $table = 'user_attendances';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function room()
    {
        return $this->belongsTo(VotingRoom::class, 'room_id', 'id');
    }

    public function convertToDateTime()
    {
        $this->join_time = Carbon::parse($this->join_time);

        return $this->join_time;
    }
}
