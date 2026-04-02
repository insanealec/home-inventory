<?php

namespace App\Actions\Notification;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;

class GetAllNotificationsAction
{
    use AsAction;

    public function handle(User $user, int $page = 1): LengthAwarePaginator
    {
        return $user->notifications()
            ->latest()
            ->paginate(25, ['*'], 'page', $page);
    }

    public function asController(Request $request): LengthAwarePaginator
    {
        return $this->handle($request->user(), (int) $request->query('page', 1));
    }
}
