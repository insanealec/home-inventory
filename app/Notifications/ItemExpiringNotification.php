<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class ItemExpiringNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  Collection<int, \App\Models\InventoryItem>  $items
     * @param  int  $windowDays  Number of days ahead that was used for the expiry window
     */
    public function __construct(
        public readonly Collection $items,
        public readonly int $windowDays = 30,
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
            ->subject("Expiry alert: {$count} " . str('item')->plural($count) . ' expiring within ' . $this->windowDays . ' days')
            ->greeting("Hi {$notifiable->name},")
            ->line("You have {$count} " . str('item')->plural($count) . " in your inventory expiring within {$this->windowDays} days:");

        foreach ($this->items->take(10) as $item) {
            $expiresIn = now()->diffInDays($item->expiration_date, false);
            $when = $expiresIn <= 0
                ? 'expired'
                : ($expiresIn === 1 ? 'expires tomorrow' : "expires in {$expiresIn} days");

            $message->line("• **{$item->name}** — {$when} ({$item->expiration_date->format('j M Y')})");
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
            'type' => 'expiring_items',
            'count' => $this->items->count(),
            'window_days' => $this->windowDays,
            'items' => $this->items->take(10)->map(fn ($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'expiration_date' => $item->expiration_date->toDateString(),
                'quantity' => $item->quantity,
            ])->all(),
        ];
    }
}
