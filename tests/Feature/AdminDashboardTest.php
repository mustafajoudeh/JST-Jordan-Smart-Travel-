<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_system_admins_can_access_financial_dashboard_data(): void
    {
        $travelerToken = User::factory()->create()->createToken('traveler')->plainTextToken;
        $contentAdminToken = User::factory()->contentAdmin()->create()->createToken('content-admin')->plainTextToken;
        $systemAdminToken = User::factory()->systemAdmin()->create()->createToken('system-admin')->plainTextToken;

        $this->withToken($travelerToken)->getJson('/api/admin/dashboard')->assertForbidden();
        Auth::forgetGuards();
        $this->withToken($contentAdminToken)->getJson('/api/admin/dashboard')->assertForbidden();
        Auth::forgetGuards();
        $this->withToken($systemAdminToken)
            ->getJson('/api/admin/dashboard?period=invalid')
            ->assertUnprocessable();
        Auth::forgetGuards();
        $this->withToken($systemAdminToken)
            ->getJson('/api/admin/dashboard?period=30d')
            ->assertOk()
            ->assertJsonPath('data.period', '30d');
        Auth::forgetGuards();
        $this->withToken($travelerToken)
            ->get('/api/admin/dashboard/export?period=30d')
            ->assertForbidden();
    }

    public function test_dashboard_period_returns_real_status_counts_and_revenue_series(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-27 14:00:00', 'Asia/Amman'));

        try {
            $admin = User::factory()->systemAdmin()->create();
            $traveler = User::factory()->create();
            $service = Service::factory()->bookable()->create(['price' => '18.000']);
            $createdAt = now()->subDays(3);

            $completed = Booking::factory()
                ->for($traveler, 'user')
                ->for($service, 'service')
                ->completed()
                ->create(['created_at' => $createdAt, 'updated_at' => $createdAt]);

            Booking::factory()->for($traveler, 'user')->for($service, 'service')->confirmed()->create();
            Booking::factory()->for($traveler, 'user')->for($service, 'service')->create(['status' => 'pending']);
            Booking::factory()->for($traveler, 'user')->for($service, 'service')->cancelled()->create();
            Booking::factory()->for($traveler, 'user')->for($service, 'service')->rejected()->create();
            Booking::factory()->for($traveler, 'user')->for($service, 'service')->create([
                'created_at' => now()->subDays(31),
                'updated_at' => now()->subDays(31),
            ]);

            $response = $this->withToken($admin->createToken('dashboard-test')->plainTextToken)
                ->getJson('/api/admin/dashboard?period=30d')
                ->assertOk();
            $data = $response->json('data');

            $this->assertSame(5, $data['kpis']['total_bookings']);
            $this->assertSame([
                'completed' => 1,
                'confirmed' => 1,
                'pending' => 1,
                'cancelled' => 1,
                'rejected' => 1,
            ], $data['booking_statuses']);
            $this->assertCount(30, $data['revenue_chart']['labels']);
            $day = array_search($createdAt->toDateString(), $data['revenue_chart']['labels'], true);
            $this->assertNotFalse($day);
            $this->assertEquals((float) $completed->total_price, $data['revenue_chart']['bookingGMV'][$day]);
            $this->assertEquals((float) $completed->commission_amount, $data['revenue_chart']['platformCommission'][$day]);
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_empty_dashboard_period_returns_zeroed_charts_and_no_activity(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-27 14:00:00', 'Asia/Amman'));

        try {
            $admin = User::factory()->systemAdmin()->create();
            $data = $this->withToken($admin->createToken('empty-dashboard-test')->plainTextToken)
                ->getJson('/api/admin/dashboard?period=30d')
                ->assertOk()
                ->json('data');

            $this->assertSame(0, $data['kpis']['total_bookings']);
            $this->assertSame([
                'completed' => 0,
                'confirmed' => 0,
                'pending' => 0,
                'cancelled' => 0,
                'rejected' => 0,
            ], $data['booking_statuses']);
            $this->assertCount(30, $data['revenue_chart']['labels']);
            $this->assertSame(array_fill(0, 30, 0), $data['revenue_chart']['bookingGMV']);
            $this->assertSame(array_fill(0, 30, 0), $data['revenue_chart']['platformCommission']);
            $this->assertSame([], $data['top_providers']);
            $this->assertCount(1, $data['recent_activities']);
            $this->assertSame('user', $data['recent_activities'][0]['type']);
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_dashboard_csv_export_is_period_scoped_and_omits_personal_data(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-27 14:00:00', 'Asia/Amman'));

        try {
            $admin = User::factory()->systemAdmin()->create();
            $traveler = User::factory()->create();
            $service = Service::factory()->bookable()->create([
                'name' => '=HYPERLINK("dashboard fixture")',
                'price' => '18.000',
            ]);
            $inside = Booking::factory()
                ->for($traveler, 'user')
                ->for($service, 'service')
                ->confirmed()
                ->create(['contact_phone' => '+962790000000']);
            $outside = Booking::factory()
                ->for($traveler, 'user')
                ->for($service, 'service')
                ->create([
                    'created_at' => now()->subDays(8),
                    'updated_at' => now()->subDays(8),
                ]);

            $response = $this->withToken($admin->createToken('dashboard-export-test')->plainTextToken)
                ->get('/api/admin/dashboard/export?period=7d')
                ->assertOk()
                ->assertHeader('content-type', 'text/csv; charset=UTF-8')
                ->assertHeader('content-disposition', 'attachment; filename=jst-bookings-7d-2026-11-27.csv');

            $content = $response->streamedContent();
            $this->assertStringStartsWith("\xEF\xBB\xBF", $content);
            $records = array_map(
                fn (string $line) => str_getcsv($line),
                preg_split('/\r\n|\n|\r/', substr($content, 3), -1, PREG_SPLIT_NO_EMPTY)
            );
            $this->assertSame([
                'Booking ID', 'Created at', 'Service date', 'Service', 'Provider', 'Participants',
                'Unit price', 'Total price', 'Currency', 'Booking status', 'Payment status',
                'Commission amount', 'Provider net',
            ], $records[0]);
            $this->assertSame("'=HYPERLINK(\"dashboard fixture\")", $records[1][3]);
            $this->assertSame((string) $inside->id, $records[1][0]);
            $this->assertNotContains((string) $outside->id, array_column(array_slice($records, 1), 0));
            $this->assertStringNotContainsString('contact_phone', $content);
            $this->assertStringNotContainsString('+962790000000', $content);
        } finally {
            Carbon::setTestNow();
        }
    }
}
