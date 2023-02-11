<?php

namespace App\Notifications;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SenderTransactionNotification extends Notification
{
    use Queueable;
    public $recipient;
    public $sender;
    public $transaction;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $recipient,User $sender,Transaction $transaction)
    {
        $this->recipient=$recipient;
        $this->sender=$sender;
        $this->transaction=$transaction;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("You've got money!")
            ->line('Hello,'.$this->sender->first_name.' '.$this->sender->last_name)
            ->line('You have successfully sent ,KES '.$this->transaction->amount.', to '.$this->recipient->username)
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
