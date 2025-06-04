<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Inventory;

class ReorderLevelAlert extends Notification
{
    use Queueable;

    public $inventory;

    public function __construct(Inventory $inventory)
    {
        $this->inventory = $inventory;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; 
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('⚠️ Reorder Level Reached: ' . $this->inventory->book->title)
            ->line("The current stock for '{$this->inventory->book->title}' is {$this->inventory->quantity}.")
            ->line("This is at or below the reorder level of {$this->inventory->book->reorder_level}.")
            ->action('View Inventory', url('/inventories'))
            ->line('Please reorder as soon as possible.');
    }
    public function toDatabase($notifiable)
{
    return [
        'book_title' => $this->inventory->book->title,
        'quantity' => $this->inventory->quantity,
        'reorder_level' => $this->inventory->book->reorder_level,
        'message' => "The book '{$this->inventory->book->title}' is at or below the reorder level.",
        'url' => url('/inventories'),
    ];
}

}
