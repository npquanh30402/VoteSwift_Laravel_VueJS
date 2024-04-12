<?php

namespace App\Enums;

namespace App\Enums;

enum BroadcastType: string
{
    case VOTING_START = 'voting_start';
    case VOTING_CHOICES = 'voting_choices';
    case VOTING_CHAT = 'voting_chat';
    case VOTING_JOIN = 'voting_join';
    case VOTING_LEAVE = 'voting_leave';
}
