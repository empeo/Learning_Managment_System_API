<?php

namespace App\Notifications;

use App\Models\Course;
use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCreatedNotification extends Notification
{
    use Queueable;

    protected $order;
    protected $user;
    protected $course;

    public function __construct(Order $order, User $user, Course $course)
    {
        $this->order = $order;
        $this->user = $user;
        $this->course = $course;
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
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('A new order has been created.')
            ->line('User: ' . $this->user->first_name . ' ' . $this->user->last_name)
            ->line('Course: ' . $this->course->name)
            ->line('Order ID: ' . $this->order->id)
            ->line('Order Count: ' . $this->order->count)
            ->line('Order Price: ' . $this->order->price)
            // ->action('View Order', url('/orders/' . $this->order->id))
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
            //
        ];
    }
}
