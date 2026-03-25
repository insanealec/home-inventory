<?php

namespace App\Actions\Notification;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Lorisleiva\Actions\Concerns\AsAction;

class MarkNotificationReadAction
{
    use AsAction;

    public function handle(User $user, string $notificationId): DatabaseNotification
    {
        $notification = $user->notifications()->findOrFail($notificationId);
        $notification->markAsRead();

        return $notification;
    }

    public function asController(Request $request, string $notificationId): DatabaseNotification
    {
        return $this->handle($request->user(), $notificationId);
    }
}
