<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class HotelSetting extends Model
{
    protected $fillable = ['key', 'value'];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('hotel_settings'));
        static::deleted(fn () => Cache::forget('hotel_settings'));
    }

    /**
     * @return array<string, string|null>
     */
    public static function all_cached(): array
    {
        return Cache::rememberForever('hotel_settings', function () {
            return static::query()->pluck('value', 'key')->all();
        });
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::all_cached()[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function currency(): string
    {
        return (string) static::get('currency', 'USD');
    }

    public static function taxRate(): float
    {
        return (float) static::get('tax_rate', 10);
    }
}
