<?php

namespace App\Actions\Notification;

use App\Models\User;
use App\Notifications\ItemExpiringNotification;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class CheckExpiringItemsAction
{
    use AsAction;

    public string $commandSignature = 'inventory:check-expiring-items {--days=30 : Number of days ahead to check for expiring items}';

    public string $commandDescription = 'Notify users of inventory items that are expiring within a configurable window';

    /**
     * Run the expiry check for all opted-in users.
     * Returns the number of users notified.
     */
    public function handle(int $windowDays = 30): int
    {
        $notified = 0;

        User::query()
            ->with(['inventoryItems' => function ($query) use ($windowDays) {
                $query->whereNotNull('expiration_date')
                    ->where('expiration_date', '<=', now()->addDays($windowDays)->endOfDay())
                    ->orderBy('expiration_date');
            }])
            ->each(function (User $user) use ($windowDays, &$notified) {
                if ($user->notificationPreferencesWithDefaults()['expiring_items'] === false) {
                    return;
                }

                $items = $user->inventoryItems;

                if ($items->isEmpty()) {
                    return;
                }

                $user->notify(new ItemExpiringNotification($items, $windowDays));
                $notified++;
            });

        return $notified;
    }

    public function asCommand(Command $command): int
    {
        $windowDays = (int) $command->option('days');
        $notified = $this->handle($windowDays);

        $command->info("Expiry notifications sent to {$notified} " . str('user')->plural($notified) . '.');

        return Command::SUCCESS;
    }
}
