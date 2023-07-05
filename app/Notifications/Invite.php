<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class Invite extends Notification
{
    use Queueable;
    protected $data;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(): MailMessage
    {
        $appName = env('APP_NAME');

        $url = $this->generateInvitationUrl();
        $name = $this->data["name"];
        $surname = $this->data["surname"];

        return (new MailMessage)
            ->subject("Povabilo za registracijo - $appName")
            ->greeting("Pozdravljeni $name $surname.")
            ->line("Povabljeni ste bili da se registrirate v aplikaciji $appName.")
            ->action('Kliknite tukaj, da se registrirate', url($url))
            ->line('Veljavnost naslova je 24 ur. Če veljavnost poteče, zaprosite za povabilo ponovno.')
            ->salutation("Lep pozdrav.");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    /**
     * Generates a unique signed URL that the mail receiver can user to register.
     * The URL contains the UserLevel and the receiver's email address, and will be valid for 1 day.
     *
     * @param $notifiable
     * @return string
     */
    public function generateInvitationUrl()
    {
        $userId = User::where('email', $this->data["email"])->first()->id;

        return URL::temporarySignedRoute('invited', now()->addDay(), [
            'userId' => $userId,
            'role' => $this->data["role"],
            'class' => $this->data["class"],
            'email' => $this->data["email"],
            'name' => $this->data["name"],
            'surname' => $this->data["surname"],
        ]);
    }
}
