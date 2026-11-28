<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminDashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'period' => $this['period'],
            'date_range' => $this['date_range'],
            'kpis' => $this['kpis'],
            'booking_statuses' => $this['booking_statuses'],
            'top_providers' => $this['top_providers'],
            'revenue_chart' => $this['revenue_chart'],
            'recent_activities' => $this['recent_activities'],
            'alerts' => $this['alerts'],
        ];
    }
}
