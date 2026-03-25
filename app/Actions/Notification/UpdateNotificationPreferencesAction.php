<?php

namespace App\Actions\Notification;

use App\Models\User;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateNotificationPreferencesAction
{
    use AsAction;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'preferences' => ['required', 'array'],
            'preferences.*' => ['boolean'],
        ];
    }

    /**
     * Validate that only known notification type keys are present.
     *
     * @param  array<string, bool>  $preferences
     */
    public function handle(User $user, array $preferences): User
    {
        // Only persist keys that are known notification types
        $filtered = array_intersect_key($preferences, User::NOTIFICATION_TYPES);

        $user->update(['notification_preferences' => $filtered]);

        return $user->fresh();
    }

    /**
     * @return array<string, bool>
     */
    public function asController(Request $request): array
    {
        $preferences = $request->validate([
            'preferences' => ['required', 'array'],
            'preferences.*' => ['boolean'],
        ])['preferences'];

        $user = $this->handle($request->user(), $preferences);

        return $user->notificationPreferencesWithDefaults();
    }
}
