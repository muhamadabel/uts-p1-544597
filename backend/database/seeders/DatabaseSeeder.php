<?php

namespace Database\Seeders;

use App\Models\Gear;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // User admin untuk pengujian dan login frontend
        User::firstOrCreate(
            ['email' => 'admin@hongfilms.com'],
            [
                'name' => 'Abel Producer',
                'password' => Hash::make('password123'),
            ]
        );

        // Seeding sampel gear produksi film
        $gears = [
            [
                'name' => 'Sony FX3 Cinema Line Full-Frame',
                'category' => 'Camera',
                'serial_number' => 'CAM-FX3-001',
                'daily_rate' => 850000.00,
                'status' => 'available',
            ],
            [
                'name' => 'Sony FE 24-70mm f/2.8 GM II',
                'category' => 'Lens',
                'serial_number' => 'LNS-GM-002',
                'daily_rate' => 350000.00,
                'status' => 'in_use',
            ],
            [
                'name' => 'Aputure LS 600d Pro Daylight LED',
                'category' => 'Lighting',
                'serial_number' => 'LGT-600D-003',
                'daily_rate' => 500000.00,
                'status' => 'available',
            ],
            [
                'name' => 'Sennheiser MKH 416-P48 Shotgun Microphone',
                'category' => 'Audio',
                'serial_number' => 'AUD-416-004',
                'daily_rate' => 250000.00,
                'status' => 'maintenance',
            ],
            [
                'name' => 'DJI RS 3 Pro Gimbal Stabilizer',
                'category' => 'Stabilizer',
                'serial_number' => 'GMB-RS3-005',
                'daily_rate' => 300000.00,
                'status' => 'available',
            ],
        ];

        foreach ($gears as $gear) {
            Gear::firstOrCreate(
                ['serial_number' => $gear['serial_number']],
                $gear
            );
        }
    }
}
