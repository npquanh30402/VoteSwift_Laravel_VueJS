<?php

namespace App\Enums;

namespace App\Enums;

enum BroadcastType: string
{
    case VOTING_START = 'voting_start';
    case VOTING_CHOICES = 'voting_choices';
    case VOTING_CHAT = 'voting_chat';
}
