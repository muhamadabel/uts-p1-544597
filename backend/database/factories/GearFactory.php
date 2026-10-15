<?php

namespace Database\Factories;

use App\Models\Gear;
use Illuminate\Database\Eloquent\Factories\Factory;

class GearFactory extends Factory
{
    protected $model = Gear::class;

    public function definition(): array
    {
        $categories = ['Camera', 'Lens', 'Lighting', 'Audio', 'Gimbal', 'Drone'];
        $statuses = ['available', 'in_use', 'maintenance'];

        return [
            'name' => fake()->randomElement([
                'Sony FX3 Cinema Line',
                'Sony A7S III',
                'Canon C70',
                'RED Komodo 6K',
                'ARRI Alexa Mini LF',
                'Sony FE 24-70mm f/2.8 GM II',
                'Sony FE 50mm f/1.2 GM',
                'Aputure 600d Pro LED Light',
                'Sennheiser MKH 416 Shotgun Mic',
                'DJI RS 3 Pro Gimbal',
                'DJI Inspire 3 Drone',
            ]),
            'category' => fake()->randomElement($categories),
            'serial_number' => 'GEAR-' . fake()->unique()->numerify('#####'),
            'daily_rate' => fake()->randomFloat(2, 250000, 2500000),
            'status' => fake()->randomElement($statuses),
        ];
    }
}
