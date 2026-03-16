<?php

namespace App\Mcp\Resources;

use App\Actions\Dashboard\GetDashboardSummaryAction;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Resource;

class DashboardResource extends Resource
{
    protected string $uri = 'inventory://dashboard';

    protected string $name = 'Inventory Dashboard';

    protected string $description = 'A summary snapshot of the inventory: total counts, items low on stock, items expiring within 30 days, and recently updated items. Read this first before deciding what action to take.';

    public function handle(Request $request): Response
    {
        return Response::json(
            app(GetDashboardSummaryAction::class)->handle($request->user())
        );
    }
}
