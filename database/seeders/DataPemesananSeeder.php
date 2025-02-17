<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataPemesanan;
use App\Models\User;
use App\Models\Tiket;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DataPemesananSeeder extends Seeder
{
    public function run()
    {
        $users = User::where('role_id', 2)->get();
        $tiket = Tiket::with('konser')->first();

        if (!$tiket || $users->isEmpty()) {
            return;
        }

        $names = [
            'John Doe', 'Jane Smith', 'Mike Johnson', 'Sarah Williams', 'David Brown',
            'Emma Wilson', 'James Taylor', 'Maria Garcia', 'Robert Chen', 'Lisa Anderson',
            'Michael Lee', 'Sophie Martin', 'Thomas White', 'Alice Cooper', 'Peter Parker'
        ];

        // Distribution of tickets across months
        $months = range(1, 12);

        // Create VIP tickets (15 tickets)
        for ($i = 0; $i < 15; $i++) {
            $randomUser = $users->random();
            $randomName = $names[array_rand($names)];
            $randomMonth = $months[array_rand($months)];
            
            DataPemesanan::create([
                'uuid' => Str::uuid(),
                'tiket_id' => $tiket->id,
                'user_id' => $randomUser->id,
                'order_id' => 'ORDER-' . Str::random(10),
                'nama_pemesan' => $randomName,
                'email_pemesan' => strtolower(str_replace(' ', '.', $randomName)) . '@example.com',
                'telepon_pemesan' => '08' . rand(1000000000, 9999999999),
                'alamat_pemesan' => 'Jl. Example No. ' . rand(1, 100),
                'tanggal_pemesan' => Carbon::now()->setMonth($randomMonth)->setDay(rand(1, 28)),
                'jumlah_tiket' => 1,
                'status_pembayaran' => 'Successfully',
                'total_harga' => $tiket->harga_vip,
                'gate_type' => 'vip',
                'is_used' => rand(0, 1),
                'created_at' => Carbon::now()->setMonth($randomMonth)->setDay(rand(1, 28)),
                'updated_at' => Carbon::now()
            ]);
        }

        // Create Regular tickets (25 tickets)
        for ($i = 0; $i < 25; $i++) {
            $randomUser = $users->random();
            $randomName = $names[array_rand($names)];
            $randomMonth = $months[array_rand($months)];
            
            DataPemesanan::create([
                'uuid' => Str::uuid(),
                'tiket_id' => $tiket->id,
                'user_id' => $randomUser->id,
                'order_id' => 'ORDER-' . Str::random(10),
                'nama_pemesan' => $randomName,
                'email_pemesan' => strtolower(str_replace(' ', '.', $randomName)) . '@example.com',
                'telepon_pemesan' => '08' . rand(1000000000, 9999999999),
                'alamat_pemesan' => 'Jl. Example No. ' . rand(1, 100),
                'tanggal_pemesan' => Carbon::now()->setMonth($randomMonth)->setDay(rand(1, 28)),
                'jumlah_tiket' => 1,
                'status_pembayaran' => 'Successfully',
                'total_harga' => $tiket->harga_regular,
                'gate_type' => 'regular',
                'is_used' => rand(0, 1),
                'created_at' => Carbon::now()->setMonth($randomMonth)->setDay(rand(1, 28)),
                'updated_at' => Carbon::now()
            ]);
        }

        // Add some pending transactions (5 tickets)
        for ($i = 0; $i < 5; $i++) {
            $randomUser = $users->random();
            $randomName = $names[array_rand($names)];
            $isVIP = rand(0, 1);
            
            DataPemesanan::create([
                'uuid' => Str::uuid(),
                'tiket_id' => $tiket->id,
                'user_id' => $randomUser->id,
                'order_id' => 'ORDER-' . Str::random(10),
                'nama_pemesan' => $randomName,
                'email_pemesan' => strtolower(str_replace(' ', '.', $randomName)) . '@example.com',
                'telepon_pemesan' => '08' . rand(1000000000, 9999999999),
                'alamat_pemesan' => 'Jl. Example No. ' . rand(1, 100),
                'tanggal_pemesan' => Carbon::now(),
                'jumlah_tiket' => 1,
                'status_pembayaran' => 'Pending',
                'total_harga' => $isVIP ? $tiket->harga_vip : $tiket->harga_regular,
                'gate_type' => $isVIP ? 'vip' : 'regular',
                'is_used' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
    }
}
