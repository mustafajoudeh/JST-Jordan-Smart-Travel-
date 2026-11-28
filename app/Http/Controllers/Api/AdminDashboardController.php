<?php

namespace App\Http\Controllers\Api;

use App\Enums\DashboardPeriod;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DashboardRequest;
use App\Http\Resources\AdminDashboardResource;
use App\Models\Destination;
use App\Models\Provider;
use App\Models\User;
use App\Models\Activity;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Payment;
use Carbon\CarbonImmutable;

class AdminDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(DashboardRequest $request)
    {
        $period = $request->validated()['period'];

        $dateRange = DashboardPeriod::from($period)->dateRange();

        $kpis = $this->getDashboardKpis($dateRange);
        $bookingStatuses = $this->getBookingStatusData($dateRange);
        $revenueChart = $this->getRevenueChartData($dateRange);
        $topProviders = $this->getTopProviders($dateRange);
        $recentActivities = $this->getRecentActivities();
        $alerts = $this->getDashboardAlerts();

        return new AdminDashboardResource([
            'period' => $period,
            'date_range' => [
                'start' => $dateRange['start']->toDateTimeString(),
                'end' => $dateRange['end']->toDateTimeString(),
            ],
            'kpis' => $kpis,
            'booking_statuses' => $bookingStatuses,
            'top_providers' => $topProviders,
            'revenue_chart' => $revenueChart,
            'recent_activities' => $recentActivities,
            'alerts' => $alerts,
        ]);
    }

    private function getDashboardKpis(array $dateRange): array
    {

        $totalDestinations = Destination::query()
            ->whereBetween('created_at', [
                $dateRange['start'],
                $dateRange['end']
            ])->count();

        $newTravelers = User::query()
            ->where('role', 'traveler')
            ->where('is_active', true)
            ->whereBetween('created_at', [
                $dateRange['start'],
                $dateRange['end']
            ])->count();

        $totalProviders = Provider::query()
            ->whereBetween('created_at', [
                $dateRange['start'],
                $dateRange['end']
            ])->count();

        $totalActivities = Activity::query()->whereBetween('created_at', [
            $dateRange['start'],
            $dateRange['end']
        ])->count();

        $totalEvents = Event::query()
            ->whereBetween('created_at', [
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
            'total_destinations' => $totalDestinations,
            'new_travelers' => $newTravelers,
            'total_providers' => $totalProviders,
            'total_activities' => $totalActivities,
            'total_events' => $totalEvents,
            'booking_gmv' => $financialTotals->booking_gmv,
            'platform_commission' => $financialTotals->platform_commission,
            'total_bookings' => $totalBookings
        ];

    }

    private function getRevenueChartData(array $dateRange): array
    {
        $start = CarbonImmutable::parse($dateRange['start'])->startOfDay();
        $end = CarbonImmutable::parse($dateRange['end'])->startOfDay();

        $data = Booking::query()
            ->selectRaw('
            DATE(created_at) as date,
            SUM(total_price) as booking_gmv,
            SUM(commission_amount) as platform_commission
        ')
            ->where('status', 'completed')
            ->whereBetween('created_at', [
                $dateRange['start'],
                $dateRange['end']
            ])
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $bookingGMV = [];
        $platformCommission = [];

        for ($date = $start; $date->lte($end); $date = $date->addDay()) {

            $formattedDate = $date->toDateString();

            $dayData = $data->get($formattedDate);

            $labels[] = $formattedDate;

            $bookingGMV[] = (float) ($dayData->booking_gmv ?? 0);

            $platformCommission[] = (float) ($dayData->platform_commission ?? 0);
        }

        return [
            'labels' => $labels,
            'bookingGMV' => $bookingGMV,
            'platformCommission' => $platformCommission,
        ];
    }

    private function getBookingStatusData(array $dateRange): array
    {
        $statuses = Booking::query()
            ->select('status')
            ->selectRaw('COUNT(*) as total')
            ->whereBetween('created_at', [
                $dateRange['start'],
                $dateRange['end']
            ])
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'completed' => (int) ($statuses['completed'] ?? 0),
            'confirmed' => (int) ($statuses['confirmed'] ?? 0),
            'pending' => (int) ($statuses['pending'] ?? 0),
            'cancelled' => (int) ($statuses['cancelled'] ?? 0),
            'rejected' => (int) ($statuses['rejected'] ?? 0),
        ];
    }

    private function getTopProviders(array $dateRange): array
    {
        return Provider::query()
            ->whereHas('bookings', function ($query) use ($dateRange) {
                $query->whereBetween('bookings.created_at', [
                    $dateRange['start'],
                    $dateRange['end']
                ]);
            })
            ->withCount([
                'bookings' => function ($query) use ($dateRange) {
                    $query->whereBetween('bookings.created_at', [
                        $dateRange['start'],
                        $dateRange['end']
                    ]);
                }
            ])
            ->withSum([
                'bookings' => function ($query) use ($dateRange) {
                    $query->where('bookings.status', 'completed')
                        ->whereBetween('bookings.created_at', [
                            $dateRange['start'],
                            $dateRange['end']
                        ]);
                }
            ], 'total_price')
            ->orderByDesc('bookings_count')
            ->limit(5)
            ->get()
            ->map(fn($provider) => [
                'name' => $provider->display_name,
                'totalBookings' => $provider->bookings_count,
                'totalGMV' => (float) ($provider->bookings_sum_total_price ?? 0),
            ])
            ->toArray();
    }

    private function getRecentActivities(): array
    {
        $limit = 5;

        $bookings = Booking::latest()
            ->take($limit)
            ->get(['id', 'created_at'])
            ->map(fn($booking) => [
                'type' => 'booking',
                'title' => 'New Booking',
                'description' => "Booking #{$booking->id}",
                'createdAt' => $booking->created_at,
            ]);

        $payments = Payment::latest()
            ->take($limit)
            ->get(['id', 'created_at'])
            ->map(fn($payment) => [
                'type' => 'payment',
                'title' => 'New Payment',
                'description' => "Payment #{$payment->id}",
                'createdAt' => $payment->created_at,
            ]);

        $users = User::latest()
            ->take($limit)
            ->get([
                'id',
                'first_name',
                'last_name',
                'created_at',
            ])
            ->map(fn($user) => [
                'type' => 'user',
                'title' => 'New User',
                'description' => "{$user->first_name} {$user->last_name}",
                'createdAt' => $user->created_at,
            ]);

        $providers = Provider::latest()
            ->take($limit)
            ->get(['id', 'display_name', 'created_at'])
            ->map(fn($provider) => [
                'type' => 'provider',
                'title' => 'New Provider',
                'description' => $provider->display_name,
                'createdAt' => $provider->created_at,
            ]);

        return $bookings
            ->concat($payments)
            ->concat($users)
            ->concat($providers)
            ->sortByDesc('createdAt')
            ->take($limit)
            ->values()
            ->map(fn($activity) => [
                ...$activity,
                'createdAt' => $activity['createdAt']->toDateTimeString(),
            ])
            ->toArray();
    }

    private function getDashboardAlerts(): array
    {
        $pendingBookings = Booking::where('status', 'pending')->count();

        $alerts = [];

        if ($pendingBookings > 0) {
            $alerts[] = [
                'type' => 'booking',
                'title' => 'Pending Bookings',
                'description' => 'Awaiting provider confirmation.',
                'count' => $pendingBookings,
                'priority' => 'warning',
            ];
        }

        return $alerts;
    }



}
