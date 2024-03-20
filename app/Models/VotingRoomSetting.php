<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VotingRoomSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'voting_room_id',
        'allow_multiple_votes',
        'public_visibility',
        'password',
        'results_visibility',
        'allow_voting',
        'allow_skipping',
        'allow_anonymous_voting'
    ];

    public function voting_room()
    {
        return $this->belongsTo(VotingRoom::class, 'voting_room_id', 'id');
    }
}
