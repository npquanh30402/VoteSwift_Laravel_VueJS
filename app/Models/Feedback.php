<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedback';

    public function decryptFeedback()
    {
        $this->feedback = Crypt::decryptString($this->feedback);

        return $this;
    }
}
