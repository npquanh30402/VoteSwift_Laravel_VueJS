<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    public function votingRoom()
    {
        return $this->belongsTo(VotingRoom::class, 'voting_room_id', 'id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id', 'id');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class, 'candidate_id', 'id');
    }

    protected function candidateImage(): Attribute
    {
        return Attribute::make(fn($value) => $value ? asset('storage/uploads/candidates/' . $value) : null);
    }
}
