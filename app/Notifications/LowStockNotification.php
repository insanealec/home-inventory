<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  Collection<int, \App\Models\InventoryItem>  $items
     */
    public function __construct(
        public readonly Collection $items,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $count = $this->items->count();
        $message = (new MailMessage)
            ->subject("Low stock alert: {$count} " . str('item')->plural($count) . ' need restocking')
            ->greeting("Hi {$notifiable->name},")
            ->line("You have {$count} " . str('item')->plural($count) . ' running low in your home inventory:');

        foreach ($this->items->take(10) as $item) {
            $message->line("• **{$item->name}** — {$item->quantity} {$item->unit} remaining (reorder point: {$item->reorder_point})");
        }

        if ($count > 10) {
            $message->line('…and ' . ($count - 10) . ' more.');
        }

        return $message
            ->action('View inventory', url('/inventory-items'))
            ->line('You can manage your notification preferences in your account settings.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'low_stock',
            'count' => $this->items->count(),
            'items' => $this->items->take(10)->map(fn ($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'quantity' => $item->quantity,
                'reorder_point' => $item->reorder_point,
                'unit' => $item->unit,
            ])->all(),
        ];
    }
}
