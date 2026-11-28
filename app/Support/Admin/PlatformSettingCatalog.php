<?php

namespace App\Support\Admin;

use Illuminate\Support\Str;

class PlatformSettingCatalog
{
    private const SETTINGS = [
        'default_commission_rate' => [
            'name' => 'Default commission rate',
            'group' => 'booking_finance',
            'group_label' => 'Bookings & revenue',
            'value_type' => 'decimal',
        ],
        'default_currency' => [
            'name' => 'Default currency',
            'group' => 'booking_finance',
            'group_label' => 'Bookings & revenue',
            'value_type' => 'string',
        ],
        'free_cancellation_hours' => [
            'name' => 'Free cancellation window',
            'group' => 'booking_policy',
            'group_label' => 'Booking policy',
            'value_type' => 'integer',
        ],
        'booking_requires_provider_approval' => [
            'name' => 'Provider approval for bookings',
            'group' => 'booking_policy',
            'group_label' => 'Booking policy',
            'value_type' => 'boolean',
        ],
    ];

    public static function definition(string $key): array
    {
        return self::SETTINGS[$key] ?? [
            'name' => Str::headline($key),
            'group' => 'other',
            'group_label' => 'Other settings',
            'value_type' => null,
        ];
    }

    public static function isProtected(string $key): bool
    {
        return isset(self::SETTINGS[$key]);
    }

    public static function protectedKeys(): array
    {
        return array_keys(self::SETTINGS);
    }

    public static function isSensitive(string $key): bool
    {
        return preg_match('/(?:password|secret|token|api[_-]?key|private[_-]?key|credential|client[_-]?secret)/i', $key) === 1;
    }

    public static function groupOptions(): array
    {
        return [
            'booking_finance' => 'Bookings & revenue',
            'booking_policy' => 'Booking policy',
            'other' => 'Other settings',
        ];
    }

    public static function valueError(string $key, string $type, string $value): ?string
    {
        $valid = match ($type) {
            'string' => true,
            'decimal' => is_numeric($value),
            'integer' => filter_var($value, FILTER_VALIDATE_INT) !== false,
            'boolean' => in_array(strtolower($value), ['true', 'false', '1', '0'], true),
            default => false,
        };

        if (! $valid) {
            return match ($type) {
                'decimal' => 'Enter a valid number.',
                'integer' => 'Enter a whole number.',
                'boolean' => 'Choose true or false.',
                default => 'Choose a supported setting type.',
            };
        }

        return match ($key) {
            'default_commission_rate' => is_numeric($value) && (float) $value >= 0 && (float) $value <= 1
                ? null
                : 'Enter a commission rate between 0 and 1.',
            'default_currency' => preg_match('/^[A-Z]{3}$/', $value) === 1
                ? null
                : 'Use a three-letter uppercase currency code.',
            'free_cancellation_hours' => filter_var($value, FILTER_VALIDATE_INT) !== false && (int) $value >= 0 && (int) $value <= 8760
                ? null
                : 'Enter a value from 0 to 8760 hours.',
            'booking_requires_provider_approval' => in_array(strtolower($value), ['true', 'false', '1', '0'], true)
                ? null
                : 'Choose true or false.',
            default => null,
        };
    }
}
