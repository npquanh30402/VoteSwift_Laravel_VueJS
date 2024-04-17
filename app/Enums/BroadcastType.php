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

    case FRIEND_REQUEST_SENT = 'friend_request_sent';
    case FRIEND_REQUEST_ACCEPTED = 'friend_request_accepted';
    case FRIEND_REQUEST_REJECTED = 'friend_request_rejected';
    case FRIEND_REQUEST_ABORTED = 'friend_request_aborted';
    case UNFRIEND = 'unfriend';
}
