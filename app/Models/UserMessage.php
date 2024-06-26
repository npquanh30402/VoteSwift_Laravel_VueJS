<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class UserMessage extends Model
{
    use HasFactory;

    protected $table = 'user_messages';

//    public function sender()
//    {
//        return $this->belongsTo(User::class, 'sender_id');
//    }
//
//    public function receiver()
//    {
//        return $this->belongsTo(User::class, 'receiver_id');
//    }

    public function decryptUserMessage()
    {
        $this->content = Crypt::decryptString($this->content);

        return $this;
    }

    protected function file(): Attribute
    {
        return Attribute::make(fn($value) => $value ? asset('storage/uploads/messages/' . $value) : null);
    }
}
