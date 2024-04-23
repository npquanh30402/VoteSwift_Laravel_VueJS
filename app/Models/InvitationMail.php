<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class InvitationMail extends Model
{
    use HasFactory;

    protected $fillable = [
        'voting_room_id',
        'mail_subject',
        'mail_content'
    ];

    protected $table = 'invitation_mails';

    public function room()
    {
        return $this->belongsTo(VotingRoom::class, 'voting_room_id', 'id');
    }

    public function decryptInvitationMail()
    {
        $this->mail_subject = Crypt::decryptString($this->mail_subject);
        $this->mail_content = Crypt::decryptString($this->mail_content);

        return $this;
    }
}
