<?php

namespace App\Actions\Notification;

use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class CheckLowStockAction
{
    use AsAction;

    public string $commandSignature = 'inventory:check-low-stock';

    public string $commandDescription = 'Notify users of inventory items that have fallen at or below their reorder point';

    /**
     * Run the low-stock check for all opted-in users.
     * Returns the number of users notified.
     */
    public function handle(): int
    {
        $notified = 0;

        User::query()
            ->with(['inventoryItems' => function ($query) {
                $query->where('reorder_point', '>', 0)
                    ->whereColumn('quantity', '<=', 'reorder_point')
                    ->orderBy('quantity');
            }])
            ->each(function (User $user) use (&$notified) {
                if ($user->notificationPreferencesWithDefaults()['low_stock'] === false) {
                    return;
                }

                $items = $user->inventoryItems;

                if ($items->isEmpty()) {
                    return;
                }

                $user->notify(new LowStockNotification($items));
                $notified++;
            });

        return $notified;
    }

    public function asCommand(Command $command): int
    {
        $notified = $this->handle();

        $command->info("Low stock notifications sent to {$notified} " . str('user')->plural($notified) . '.');

        return Command::SUCCESS;
    }
}
