<?php

namespace App\Actions\Notification;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Lorisleiva\Actions\Concerns\AsAction;

class GetNotificationsAction
{
    use AsAction;

    public function handle(User $user): DatabaseNotificationCollection
    {
        return $user->notifications()
            ->latest()
            ->limit(50)
            ->get();
    }

    public function asController(Request $request): DatabaseNotificationCollection
    {
        return $this->handle($request->user());
    }
}
