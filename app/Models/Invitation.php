<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $table = "invitations";

    protected $fillable = [
        'voting_room_id',
        'invited_user_id',
        'accepted',
    ];

    public function room()
    {
        return $this->belongsTo(VotingRoom::class, 'voting_room_id', 'id');
    }

    public function invitedUser()
    {
        return $this->belongsTo(User::class, 'invited_user_id', 'id');
    }
}
