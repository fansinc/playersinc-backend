<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPassword extends Notification
{
    use Queueable;

    public $token;

    public $url;

    public $email;

    public function __construct($token,$url,$email)
    {
        $this->token = $token;
        $this->url = $url;
        $this->email = $email;
    }

    
    
    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
     
        return (new MailMessage)
            ->subject('Your Fansinc Account Password Reset Link')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', url( $this->url."?email=".$this->email."&token=".$this->token, $this->token))
            ->line('If you did not request a password reset, no further action is required.');
    }
}
