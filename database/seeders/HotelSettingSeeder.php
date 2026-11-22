<?php

namespace Database\Seeders;

use App\Models\HotelSetting;
use Illuminate\Database\Seeder;

class HotelSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'hotel_name' => 'De La Renaissance',
            'address' => '1 Boulevard de la Renaissance',
            'phone' => '+855 23 000 000',
            'email' => 'reservations@delarenaissance.example',
            'currency' => 'USD',
            'tax_rate' => '10', // VAT %, applied on top of the room subtotal
            'check_in_time' => '14:00',
            'check_out_time' => '12:00',
            // KHQR / Bakong payments (Cambodia). Replace with your real
            // Bakong account to accept live payments via a KHQR gateway.
            'khqr_enabled' => '1',
            'bakong_account_id' => 'de_la_renaissance@bakong',
            'bakong_account_name' => 'DE LA RENAISSANCE HOTEL',
        ];

        foreach ($settings as $key => $value) {
            HotelSetting::set($key, $value);
        }
    }
}
