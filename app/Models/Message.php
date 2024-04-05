<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Message extends Model
{
    use HasFactory;

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public static function unread(User $user)
    {
        return self::where('receiver_id', Auth::user()->id)
            ->where('sender_id', $user->id)
            ->where('is_read', false);
    }

    public function read()
    {
        return $this->update(['is_read' => true]);
    }

    protected function file(): Attribute
    {
        return Attribute::make(fn($value) => $value ? asset('storage/uploads/messages/' . $value) : null);
    }
}
