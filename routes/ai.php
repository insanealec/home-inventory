<?php

use App\Mcp\Servers\HomeInventoryServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::web('/mcp', HomeInventoryServer::class)->middleware(['auth:sanctum']);
