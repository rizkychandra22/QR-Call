<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Shift::create([
            'shift_name' => 'Pagi Grup A',
            'shift_code' => 'PGA01',
            'in_time' => '07:00',
            'out_time' => '15:00',
        ]);

        Shift::create([
            'shift_name' => 'Siang Grup A',
            'shift_code' => 'SGA02',
            'in_time' => '13:00',
            'out_time' => '21:00',
        ]);

        Shift::create([
            'shift_name' => 'Malam Grup A',
            'shift_code' => 'MGA03',
            'in_time' => '19:00',
            'out_time' => '03:00',
        ]);
    }
}
