<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_title',
        'candidate_description',
        'candidate_image',
        'question_id'
    ];

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id', 'id');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class, 'candidate_id', 'id');
    }

    public function decryptCandidate()
    {
        if ($this->candidate_title) {
            $this->candidate_title = Crypt::decryptString($this->candidate_title);
        }

        if ($this->candidate_description) {
            $this->candidate_description = Crypt::decryptString($this->candidate_description);
        }

        return $this;
    }

    protected function candidateImage(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? asset('storage/uploads/candidates/' . $value) : null,
        );
    }
}
