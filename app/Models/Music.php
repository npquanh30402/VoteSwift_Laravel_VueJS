<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Music extends Model
{
    use HasFactory;

    protected $table = 'music';

    protected $fillable = [
        'user_id',
        'title',
        'url'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    protected function url(): Attribute
    {
        return Attribute::make(fn($value) => $value ? asset('storage/uploads/music/' . $value) : null);
    }
}
