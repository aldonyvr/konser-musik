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
            'tiket_tersedia' => '300',
            'reguler' => '250',
            'gate_a_capacity' => '50',
            'gate_b_capacity' => '50',
            'gate_c_capacity' => '50',
            'gate_d_capacity' => '50',
            'gate_e_capacity' => '50',
            'vip' => '100',
            'harga_vip' => '150000',
            'harga_regular' => '100000',
            'opengate' => '08:00',
            'closegate' => '10:00',
       ]);
       Tiket::create([
            'konsers_id' => 2,
            'tiket_tersedia' => '200',
            'reguler' => '150',
            'gate_a_capacity' => '50',
            'gate_b_capacity' => '50',
            'gate_c_capacity' => '50',
            'vip' => '100',
            'harga_vip' => '200000',
            'harga_regular' => '150000',
            'opengate' => '16:00',
            'closegate' => '19:00',
       ]);
       Tiket::create([
            'konsers_id' => 3,
            'tiket_tersedia' => '300',
            'reguler' => '250',
            'gate_a_capacity' => '50',
            'gate_b_capacity' => '50',
            'gate_c_capacity' => '50',
            'gate_d_capacity' => '50',
            'gate_e_capacity' => '50',
            'vip' => '100',
            'harga_vip' => '150000',
            'harga_regular' => '100000',
            'opengate' => '18:00',
            'closegate' => '20:00',
       ]);
       Tiket::create([
            'konsers_id' => 4,
            'tiket_tersedia' => '150',
            'reguler' => '100',
            'gate_a_capacity' => '50',
            'gate_b_capacity' => '50',
            'gate_c_capacity' => '50',
            'vip' => '100',
            'harga_vip' => '150000',
            'harga_regular' => '100000',
            'opengate' => '18:00',
            'closegate' => '20:00',
       ]);
       Tiket::create([
            'konsers_id' => 5,
            'tiket_tersedia' => '300',
            'reguler' => '250',
            'gate_a_capacity' => '50',
            'gate_b_capacity' => '50',
            'gate_c_capacity' => '50',
            'gate_d_capacity' => '50',
            'gate_e_capacity' => '50',
            'vip' => '100',
            'harga_vip' => '150000',
            'harga_regular' => '100000',
            'opengate' => '18:00',
            'closegate' => '20:00',
       ]);
       Tiket::create([
            'konsers_id' => 6,
            'tiket_tersedia' => '200',
            'reguler' => '150',
            'gate_a_capacity' => '50',
            'gate_b_capacity' => '50',
            'gate_c_capacity' => '50',
            'vip' => '100',
            'harga_vip' => '150000',
            'harga_regular' => '100000',
            'opengate' => '18:00',
            'closegate' => '20:00',
       ]);
       Tiket::create([
            'konsers_id' => 7,
            'tiket_tersedia' => '200',
            'reguler' => '150',
            'gate_a_capacity' => '50',
            'gate_b_capacity' => '50',
            'gate_c_capacity' => '50',
            'vip' => '100',
            'harga_vip' => '150000',
            'harga_regular' => '100000',
            'opengate' => '18:00',
            'closegate' => '20:00',
       ]);
       Tiket::create([
            'konsers_id' => 8,
            'tiket_tersedia' => '200',
            'reguler' => '150',
            'gate_a_capacity' => '50',
            'gate_b_capacity' => '50',
            'gate_c_capacity' => '50',
            'vip' => '100',
            'harga_vip' => '150000',
            'harga_regular' => '100000',
            'opengate' => '18:00',
            'closegate' => '20:00',
       ]);
       Tiket::create([
            'konsers_id' => 9,
            'tiket_tersedia' => '200',
            'reguler' => '150',
            'gate_a_capacity' => '50',
            'gate_b_capacity' => '50',
            'gate_c_capacity' => '50',
            'vip' => '100',
            'harga_vip' => '150000',
            'harga_regular' => '100000',
            'opengate' => '18:00',
            'closegate' => '20:00',
       ]);
       Tiket::create([
            'konsers_id' => 10,
       ]);
       Tiket::create([
            'konsers_id' => 11,
       ]);
       Tiket::create([
            'konsers_id' => 12,
       ]);
    }
}