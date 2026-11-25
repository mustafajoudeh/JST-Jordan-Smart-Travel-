<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Destination;
use App\Models\Provider;
use App\Models\User;
use App\Models\Activity;
use App\Models\Booking;
use App\Models\Event;

use Carbon\CarbonImmutable;


class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {

        $period = $request->query('period', '30d');

        $allowedPeriods = [
            'today',
            '7d',
            '30d',
            '90d',
            'this_month',
        ];

        if (!in_array($period, $allowedPeriods, true)) {
            $period = '30d';
        }

        $dateRange = $this->getDateRange($period);

        $kpis = $this->getDashboardKpis($dateRange);

        dd($kpis);
    }


    private function getDashboardKpis(array $dateRange): array
    {

        $TotalDestinations = Destination::
            whereBetween('created_at', [
                $dateRange['start'],
                $dateRange['end']
            ])->count();

        $TotalActiveTravelers = User::
            where('role', 'traveler')
            ->where('is_active', true)
            ->whereBetween('created_at', [
                $dateRange['start'],
                $dateRange['end']
            ])->count();

        $TotalProviders = Provider::
            whereBetween('created_at', [
                $dateRange['start'],
                $dateRange['end']
            ])->count();

        $TotalActivities = Activity::whereBetween('created_at', [
            $dateRange['start'],
            $dateRange['end']
        ])->count();

        $TotalEvents = Event::
            whereBetween('created_at', [
                $dateRange['start'],
                $dateRange['end']
            ])->count();

        $financialTotals = Booking::query()
            ->where('status', 'completed')
            ->whereBetween('created_at', [
                $dateRange['start'],
                $dateRange['end']
            ])
            ->selectRaw('
            COALESCE(SUM(total_price), 0) as booking_gmv,
            COALESCE(SUM(commission_amount), 0) as platform_commission
        ')
            ->first();

        $totalBookings = Booking::query()
            ->whereBetween('created_at', [
                $dateRange['start'],
                $dateRange['end']
            ])
            ->count();

        return [
            'total_destinations' => $TotalDestinations,
            'total_active_travelers' => $TotalActiveTravelers,
            'total_providers' => $TotalProviders,
            'total_activities' => $TotalActivities,
            'total_events' => $TotalEvents,
            'booking_gmv' => $financialTotals->booking_gmv,
            'platform_commission' => $financialTotals->platform_commission,
            'total_bookings' => $totalBookings
        ];

    }



    private function getDateRange(string $period): array
    {
        $now = CarbonImmutable::now();

        return match ($period) {
            'today' => [
                'start' => $now->startOfDay(),
                'end' => $now,
            ],

            '7d' => [
                'start' => $now->subDays(6)->startOfDay(),
                'end' => $now,
            ],

            '30d' => [
                'start' => $now->subDays(29)->startOfDay(),
                'end' => $now,
            ],

            '90d' => [
                'start' => $now->subDays(89)->startOfDay(),
                'end' => $now,
            ],

            'this_month' => [
                'start' => $now->startOfMonth(),
                'end' => $now,
            ],

            default => [
                'start' => $now->subDays(29)->startOfDay(),
                'end' => $now,
            ],
        };
    }
}