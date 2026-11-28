<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\AdminDashboardController as ApiAdminDashboardController;
use App\Http\Requests\Admin\DashboardRequest;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminDashboardController extends Controller
{
    public function index(DashboardRequest $request): JsonResource
    {
        return app(ApiAdminDashboardController::class)->index($request);
    }
}
