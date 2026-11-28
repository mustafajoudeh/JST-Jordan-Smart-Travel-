<?php

namespace App\Enums;

use Carbon\CarbonImmutable;

enum DashboardPeriod: string
{
    case Today = 'today';
    case LastSevenDays = '7d';
    case LastThirtyDays = '30d';
    case LastNinetyDays = '90d';
    case ThisMonth = 'this_month';

    public static function values(): array
    {
        return array_map(fn (self $period) => $period->value, self::cases());
    }

    public function dateRange(): array
    {
        $now = CarbonImmutable::now();

        return match ($this) {
            self::Today => ['start' => $now->startOfDay(), 'end' => $now],
            self::LastSevenDays => ['start' => $now->subDays(6)->startOfDay(), 'end' => $now],
            self::LastThirtyDays => ['start' => $now->subDays(29)->startOfDay(), 'end' => $now],
            self::LastNinetyDays => ['start' => $now->subDays(89)->startOfDay(), 'end' => $now],
            self::ThisMonth => ['start' => $now->startOfMonth(), 'end' => $now],
        };
    }
}
