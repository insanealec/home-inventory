<?php

namespace App\Actions\Notification;

use App\Models\User;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class GetNotificationPreferencesAction
{
    use AsAction;

    /**
     * @return array<string, bool>
     */
    public function handle(User $user): array
    {
        return $user->notificationPreferencesWithDefaults();
    }

    /**
     * @return array<string, bool>
     */
    public function asController(Request $request): array
    {
        return $this->handle($request->user());
    }
}
