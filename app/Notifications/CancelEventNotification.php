<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CancelEventNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $event_name;
    public $username;

  public function __construct($event_name, $username)
  {
      $this->event_name = $event_name;
      $this->username = $username;
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
      return (new MailMessage())
      ->subject('Annulation de l\'événement ' . $this->event_name)
      ->line('Nous sommes au regret de vous informer, ' . $this->username . ', que l\'événement pour lequel vous étiez inscrit a été annulé.')
      ->line('Nous vous prions de nous excuser pour tout inconvénient que cela pourrait causer.')
      ->line('Si vous avez des questions ou avez besoin d\'informations supplémentaires, n\'hésitez pas à nous contacter à l\'adresse suivante : ' . env('MAIL_FROM_ADDRESS', 'example@example.com') . '.');
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
