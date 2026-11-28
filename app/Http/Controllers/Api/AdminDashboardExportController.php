<?php

namespace App\Http\Controllers\Api;

use App\Enums\DashboardPeriod;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DashboardRequest;
use App\Models\Booking;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminDashboardExportController extends Controller
{
    public function export(DashboardRequest $request): StreamedResponse
    {
        $period = DashboardPeriod::from($request->validated()['period']);
        $dateRange = $period->dateRange();
        $filename = 'jst-bookings-'.$period->value.'-'.now()->format('Y-m-d').'.csv';
        $bookings = Booking::query()
            ->with(['service.provider'])
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->orderBy('created_at')
            ->orderBy('id')
            ->lazy(500);

        return response()->streamDownload(function () use ($bookings): void {
            $stream = fopen('php://output', 'wb');
            fwrite($stream, "\xEF\xBB\xBF");
            fputcsv($stream, [
                'Booking ID',
                'Created at',
                'Service date',
                'Service',
                'Provider',
                'Participants',
                'Unit price',
                'Total price',
                'Currency',
                'Booking status',
                'Payment status',
                'Commission amount',
                'Provider net',
            ], ',', '"', '');

            foreach ($bookings as $booking) {
                fputcsv($stream, [
                    $booking->id,
                    $booking->created_at?->toDateTimeString(),
                    $booking->booking_date?->toDateString(),
                    $this->safeCell($booking->service?->name),
                    $this->safeCell($booking->service?->provider?->display_name),
                    $booking->participants,
                    (string) $booking->unit_price,
                    (string) $booking->total_price,
                    $booking->currency,
                    $booking->status,
                    $booking->payment_status,
                    (string) $booking->commission_amount,
                    (string) $booking->provider_net,
                ], ',', '"', '');
            }

            fclose($stream);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    private function safeCell(?string $value): string
    {
        $value = str_replace(["\r\n", "\r"], "\n", $value ?? '');

        return preg_match('/^[\t ]*[=+\-@]/u', $value) ? "'".$value : $value;
    }
}
