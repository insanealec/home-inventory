<?php

namespace App\Mcp\Tools;

use App\Actions\Dashboard\GetDashboardSummaryAction;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetDashboardTool extends Tool
{
    protected string $name = 'get_dashboard';

    protected string $description = 'Get a summary snapshot of the inventory: total counts, items low on stock, items expiring within 30 days, and recently updated items. Read this first before deciding what action to take.';

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function handle(Request $request): Response
    {
        return Response::json(
            app(GetDashboardSummaryAction::class)->handle($request->user())
        );
    }
}
