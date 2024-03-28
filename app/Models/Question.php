<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

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

    public function decryptQuestion()
    {
        $this->question_title = Crypt::decryptString($this->question_title);
        $this->question_description = Crypt::decryptString($this->question_description);
    }

    protected function questionImage(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? asset('storage/uploads/questions/' . $value) : null,
        );
    }
}
