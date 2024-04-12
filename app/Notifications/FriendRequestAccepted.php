<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FriendRequestAccepted extends Notification implements ShouldQueue, ShouldBroadcastNow
{
    use Queueable;

    private User $recipient;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $recipient)
    {
        $this->recipient = $recipient;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('Your friend request has been accepted')
            ->action('Visit your profile', route('profile', $this->recipient->username))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'recipient_id' => $this->recipient->id,
            'recipient_username' => $this->recipient->username,
            'recipient_avatar' => $this->recipient->avatar
        ];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        $notification = \App\Models\Notification::find($this->id);

        return new BroadcastMessage($notification->toArray());
    }
}
