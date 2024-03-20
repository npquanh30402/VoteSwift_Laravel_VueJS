<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    public function room()
    {
        return $this->belongsTo(VotingRoom::class, 'voting_room_id', 'id');
    }

    public function candidates()
    {
        return $this->hasMany(Candidate::class, 'question_id', 'id');
    }
}
