<?php

namespace App\Notifications;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DepositFundsNotification extends Notification
{
    use Queueable;
    public $user;
    public $transaction;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user,Transaction $transaction)
    {
        $this->user=$user;
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
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Funds Deposit')
                    ->line('Hello, '.$this->user->username)
                    ->line('You have successfully deposited money into account '.$this->transaction->receiver_account_number)
                    ->action('Check balance here', url('/'))
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
