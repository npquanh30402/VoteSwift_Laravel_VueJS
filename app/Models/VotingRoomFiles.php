<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VotingRoomFiles extends Model
{
    use HasFactory;

    protected $table = 'voting_room_files';

    protected $fillable = ['voting_room_id', 'file_name', 'file_path'];

    public function votingRoom()
    {
        return $this->belongsTo(VotingRoom::class, 'voting_room_id', 'id');
    }

    protected function filePath(): Attribute
    {
        return Attribute::make(fn($value) => $value ? asset('storage/uploads/rooms/' . $value) : null);
    }
}
