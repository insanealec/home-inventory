<?php

namespace App\Actions\Notification;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class DismissNotificationAction
{
    use AsAction;

    public function handle(User $user, string $notificationId): void
    {
        $notification = $user->notifications()->findOrFail($notificationId);
        $notification->delete();
    }

    public function asController(Request $request, string $notificationId): Response
    {
        $this->handle($request->user(), $notificationId);

        return response()->noContent();
    }
}
