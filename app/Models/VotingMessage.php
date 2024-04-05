<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VotingMessage extends Model
{
    use HasFactory;

    protected $table = 'voting_messages';

    protected function file(): Attribute
    {
        return Attribute::make(fn($value) => $value ? asset('storage/uploads/messages/' . $value) : null);
    }
}
