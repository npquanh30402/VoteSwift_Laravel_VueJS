<?php

use App\Http\Controllers\Api\CandidateController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\FriendController;
use App\Http\Controllers\Api\ImageUploadController;
use App\Http\Controllers\Api\InvitationController;
use App\Http\Controllers\Api\InvitationMailController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\NotificationSeenController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\UserJoinTimeController;
use App\Http\Controllers\Api\UserMessageController;
use App\Http\Controllers\Api\VoteController;
use App\Http\Controllers\Api\VotingChatController;
use App\Http\Controllers\Api\VotingRoomAttachmentController;
use App\Http\Controllers\Api\VotingRoomController;
use App\Http\Controllers\Api\VotingRoomSettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/images/{room}/upload', [VotingRoomAttachmentController::class, 'store'])->name('api.room.attachment.store');

Route::group(['middleware' => 'web'], function () {
    Route::get('/room/{room}/join-times', [UserJoinTimeController::class, 'index'])->name('api.user.join.time.index');

    Route::post('/images/upload', [ImageUploadController::class, 'store'])->name('api.image.upload');

    Route::get('/notifications/', [NotificationController::class, 'index'])->name('api.notifications.index');
    Route::get('/notifications/unreadCount', [NotificationController::class, 'unreadCount'])->name('api.notifications.unreadCount');
    Route::put('notification/{notification}/mark-as-read', NotificationSeenController::class)->name('api.notification.read');
    Route::put('user/{user}notification/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('api.notification.all.read');

    Route::get('/rooms/', [VotingRoomController::class, 'index'])->name('api.room.index');
    Route::get('/room/{room}', [VotingRoomController::class, 'fetchAVotingRoom'])->name('api.room.show');
    Route::post('/room/', [VotingRoomController::class, 'store'])->name('api.room.store');
    Route::put('/room/{room}', [VotingRoomController::class, 'update'])->name('api.room.update');
    Route::delete('/room/{room}', [VotingRoomController::class, 'delete'])->name('api.room.destroy');

    Route::get('/room/{room}/duplicate', [VotingRoomController::class, 'duplicate'])->name('api.room.duplicate');

    Route::get('/room/{room}/attachments', [VotingRoomAttachmentController::class, 'index'])->name('api.room.attachment.index');
    Route::delete('/attachment/{attachment}', [VotingRoomAttachmentController::class, 'destroy'])->name('api.attachment.destroy');

    Route::get('/search', [UserController::class, 'search'])->name('user.search');

    Route::get('/room/{room}/invitations', [InvitationController::class, 'getInvitations'])->name('api.room.invitation.index');
    Route::post('/room/{room}/invitations', [InvitationController::class, 'store'])->name('api.room.invitation.store');

    Route::post('/room/{room}/csv', [InvitationController::class, 'importInvitationsFromCSV'])->name('api.room.invitation.csv');

    Route::get('/room/{room}/invitations/mail', [InvitationMailController::class, 'index'])->name('api.room.invitation.mail.index');
    Route::post('/room/{room}/invitations/mail', [InvitationMailController::class, 'storeOrUpdate'])->name('api.room.invitation.mail.store');
    Route::delete('/invitations/{invitationMail}/mail', [InvitationMailController::class, 'destroy'])->name('api.room.invitation.mail.destroy');

    Route::get('/room/{room}/settings/', [VotingRoomSettingController::class, 'getSettings'])->name('api.room.setting.index');
    Route::put('/room/{room}/settings/', [VotingRoomSettingController::class, 'updateSettings'])->name('api.room.setting.update');


//    Route::get('/chat/{user}', [ChatController::class, 'index'])->name('api.user.chat.index');
//    Route::post('/chat/{user}', [ChatController::class, 'store'])->name('api.user.chat.store');
//    Route::get('/chat/', [ChatController::class, 'getUnreadAll'])->name('api.user.chat.unread.all');
//    Route::post('/chat/{user}/read', [ChatController::class, 'markRead'])->name('api.user.chat.read.all');

    Route::get('/chat/sender/{sender}/receiver/{receiver}', [UserMessageController::class, 'index'])->name('api.user.chat.index');
    Route::get('/chat/user/{user}/unread', [UserMessageController::class, 'fetchUnreadMessages'])->name('api.user.chat.unread');
    Route::post('/chat/sender/{sender}/receiver/{receiver}', [UserMessageController::class, 'store'])->name('api.user.chat.store');
    Route::post('/chat/sender/{sender}/receiver/{receiver}/mark-read', [UserMessageController::class, 'markAsRead'])->name('api.user.chat.mark-read');

    Route::get('/friends', [FriendController::class, 'getFriends'])->name('api.user.friend.index');

    Route::post('/profile/sender/{sender}/receiver/{receiver}/unfriend', [FriendController::class, 'unfriend'])->name('api.user.unfriend');
    Route::post('/profile/sender/{sender}/receiver/{receiver}/send-friend-request', [FriendController::class, 'sendFriendRequest'])->name('api.user.send-friend-request');
    Route::post('/profile/sender/{sender}/receiver/{receiver}/accept-friend-request', [FriendController::class, 'acceptFriendRequest'])
        ->name('api.user.accept-friend-request');
    Route::post('/profile/sender/{sender}/receiver/{receiver}/reject-friend-request/', [FriendController::class, 'rejectFriendRequest'])
        ->name('api.user.reject-friend-request');
    Route::post('/profile/sender/{sender}/receiver/{receiver}/abort-request-sent/', [FriendController::class, 'abortRequestSent'])
        ->name('api.user.abort-request-sent');


    Route::get('/room/{room}/questions', [QuestionController::class, 'index'])->name('api.room.question.index');
    Route::post('/room/{room}/questions', [QuestionController::class, 'store'])->name('api.room.question.store');
    Route::put('/room/questions/{question}', [QuestionController::class, 'update'])->name('api.room.question.update');
    Route::delete('/question/{question}', [QuestionController::class, 'delete'])->name('api.room.question.destroy');

    Route::get('/question/{question}/candidates', [CandidateController::class, 'QuestionCandidates'])->name('api.question.candidate.index');
    Route::get('/room/{room}/candidates', [CandidateController::class, 'RoomCandidates'])->name('api.room.candidate.index');
    Route::post('/question/{question}/candidates', [CandidateController::class, 'store'])->name('api.question.candidate.store');
    Route::put('/candidate/{candidate}', [CandidateController::class, 'update'])->name('api.candidate.update');
    Route::delete('/candidate/{candidate}', [CandidateController::class, 'destroy'])->name('api.candidate.destroy');

    Route::get('/voting/{room}/chat/', [VotingChatController::class, 'index'])->name('api.vote.chat.index');
    Route::post('/voting/{room}/chat/', [VotingChatController::class, 'store'])->name('api.vote.chat.store');

    Route::get('/room/{room}/start', [VoteController::class, 'startVote'])->name('api.room.vote.start');
    Route::get('/room/{room}/results', [VoteController::class, 'getVoteResults'])->name('api.room.vote.results');
    Route::post('/room/{room}/choice', [VoteController::class, 'broadcastChoice'])->name('api.room.vote.broadcast.choice');
    Route::post('/room/{room}/vote', [VoteController::class, 'storeVotes'])->name('api.room.vote.store');

    Route::get('/room/{room}/join', [VoteController::class, 'getJoinTimes'])->name('api.room.vote.get.join.times');
    Route::post('/room/{room}/join', [VoteController::class, 'storeJoinTime'])->name('api.room.vote.store.join.time');
    Route::post('/room/{room}/leave', [VoteController::class, 'storeLeaveTime'])->name('api.room.vote.store.leave.time');
    Route::delete('/room/{room}/user/{user}/join', [VoteController::class, 'deleteJoinTime'])->name('api.room.vote.delete.join.time');

    Route::get('/room/{room}/choices', [VoteController::class, 'getUserChoices'])->name('api.room.vote.get.choices');
    Route::post('/room/{room}/choices', [VoteController::class, 'storeUserChoices'])->name('api.room.vote.store.choices');
    Route::delete('/room/{room}/user/{user}/choices', [VoteController::class, 'deleteUserChoices'])->name('api.room.vote.delete.choices');

    Route::post('/room/{room}/user/{user}/feedback', [FeedbackController::class, 'store'])->name('api.room.user.feedback.store');

    Route::get('/room/{room}/votes', [VoteController::class, 'getVotes'])->name('api.room.votes.get');
    Route::get('/room/{room}/result-page-votes', [VoteController::class, 'getResultPageVotes'])->name('api.room.vote.results.get');
});
