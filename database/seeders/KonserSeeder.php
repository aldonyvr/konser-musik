<?php

namespace Database\Seeders;

use App\Models\Konser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KonserSeeder extends Seeder
{
    public function run()
    {
            Konser::create([
                'title' => 'Arditho Pramono',
                'tanggal' => '2024-12-14',
                'lokasi' => 'YOGYAKARTA',
                'harga' => '50000',
                'jam' => '20.00',
                'tiket_tersedia' => '150',
                'image' => '/media/musik/ardhito.png',
                // 'kontak' => '0821173612731',
                'deskripsi' => 'Konser musik terbesar di Jakarta.',
            ]);
            Konser::create([
                'title' => 'Hindia Music Festival',
                'tanggal' => '2024-11-24',
                'lokasi' => 'SURABAYA',
                'harga' => '50000',
                'tiket_tersedia' => '150',
                'jam' => '19.00',
                'image' => '/media/musik/baskara.png',
                // 'kontak' => '0821173612731',
                'deskripsi' => 'Konser musik terbesar di Jakarta.',
            ]);
            Konser::create([
                'title' => 'Sal Priadi',
                'tanggal' => '2024-11-29',
                'lokasi' => 'SURABAYA',
                'harga' => '75000',
                'tiket_tersedia' => '150',
                'jam' => '20.00 ',
                'image' => '/media/musik/sal priadi.png',
                // 'kontak' => '081234273171',
                'deskripsi' => 'Festival musik rock terbesar di Bali.',
            ]);
            Konser::create([
                'title' => 'KUNTO AJI',
                'tanggal' => '2024-11-30',
                'lokasi' => 'JAKARTA',
                'harga' => '100000',
                'jam' => '20.00 ',
                'tiket_tersedia' => '150',
                'image' => '/media/musik/kuntoaji.png',
                // 'kontak' => '085526315261',
                'deskripsi' => 'Malam jazz yang menyenangkan di Yogyakarta.',
            ]);
            Konser::create([
                'title' => 'Nadin Amizah',
                'tanggal' => '2025-01-10',
                'lokasi' => 'SURABAYA',
                'harga' => '60000',
                'jam' => '20.00 ',
                'tiket_tersedia' => '150',
                'image' => '/media/musik/nadin.png',
                // 'kontak' => '087327362731',
                'deskripsi' => 'Malam jazz yang menyenangkan di Yogyakarta.',
            ]);
            Konser::create([
                'title' => 'Lana Del Rey',
                'tanggal' => '2025-01-24',
                'lokasi' => 'JAKARTA',
                'harga' => '65000',
                'jam' => '20.00 ',
                'tiket_tersedia' => '150',
                'image' => '/media/musik/ldr.png',
                // 'kontak' => '08426341232',
                'deskripsi' => 'Konser musik terbesar di Jakarta.',
            ]);
            Konser::create([
                'title' => 'The 1975',
                'tanggal' => '2025-02-15',
                'lokasi' => 'JAKARTA',
                'harga' => '50000',
                'jam' => '20.00',
                'tiket_tersedia' => '150',
                'image' => '/media/musik/the1975.png',
                // 'kontak' => '08632642731',
                'deskripsi' => 'Konser musik terbesar di Jakarta.',
            ]);
            Konser::create([
                'title' => 'Cigarettes after sex',
                'tanggal' => '2024-12-14',
                'lokasi' => 'JAKARTA',
                'harga' => '50000',
                'jam' => '20.00',
                'tiket_tersedia' => '150',
                'image' => '/media/musik/cas.png',
                // 'kontak' => '086723467236',
                'deskripsi' => 'Konser musik terbesar di Jakarta.',
            ]);
            // Konser::create([
            //     'title' => 'Artic Monkey',
            //     'tanggal' => '2024-12-14',
            //     'lokasi' => 'SURABAYA',
            //     'harga' => '50000',
            //     'jam' => '20.00',
            //     'tiket_tersedia' => '150',
            //     'image' => '/media/musik/cas.png',
            //     'kontak' => '086723467236',
            //     'deskripsi' => 'Konser musik terbesar di Jakarta.',
            // ]);
            
    }
}
