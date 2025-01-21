<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tiket;

class TiketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
       Tiket::create([
            'konsers_id' => 1,
            'tiket_tersedia' => '150',
            'reguler' => '100',
            'vip' => '50',
            'harga_vip' => '150000',
            'harga_regular' => '100000',
            'opengate' => '08:00',
            'closegate' => '10:00',
       ]);
       Tiket::create([
            'konsers_id' => 2,
            'tiket_tersedia' => '150',
            'reguler' => '100',
            'vip' => '50',
            'harga_vip' => '200000',
            'harga_regular' => '150000',
            'opengate' => '16:00',
            'closegate' => '19:00',
       ]);
       Tiket::create([
            'konsers_id' => 3,
            'tiket_tersedia' => '150',
            'reguler' => '100',
            'vip' => '50',
            'harga_vip' => '150000',
            'harga_regular' => '100000',
            'opengate' => '18:00',
            'closegate' => '20:00',
       ]);
       Tiket::create([
            'konsers_id' => 4,
            'tiket_tersedia' => '150',
            'reguler' => '100',
            'vip' => '50',
            'harga_vip' => '150000',
            'harga_regular' => '100000',
            'opengate' => '18:00',
            'closegate' => '20:00',
       ]);
       Tiket::create([
            'konsers_id' => 5,
            'tiket_tersedia' => '150',
            'reguler' => '100',
            'vip' => '50',
            'harga_vip' => '150000',
            'harga_regular' => '100000',
            'opengate' => '18:00',
            'closegate' => '20:00',
       ]);
       Tiket::create([
            'konsers_id' => 6,
            'tiket_tersedia' => '150',
            'reguler' => '100',
            'vip' => '50',
            'harga_vip' => '150000',
            'harga_regular' => '100000',
            'opengate' => '18:00',
            'closegate' => '20:00',
       ]);
       Tiket::create([
            'konsers_id' => 7,
            'tiket_tersedia' => '150',
            'reguler' => '100',
            'vip' => '50',
            'harga_vip' => '150000',
            'harga_regular' => '100000',
            'opengate' => '18:00',
            'closegate' => '20:00',
       ]);
       Tiket::create([
            'konsers_id' => 8,
            'tiket_tersedia' => '150',
            'reguler' => '100',
            'vip' => '50',
            'harga_vip' => '150000',
            'harga_regular' => '100000',
            'opengate' => '18:00',
            'closegate' => '20:00',
       ]);
    }
}