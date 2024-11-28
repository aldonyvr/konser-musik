<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('settings')->truncate();

        Setting::create([
            'app' => 'KONSER MUSIK',
            'description' =>  'Website Pemesanan Tiket Konser Musik',
            'logo' =>  '/media/musik/logo.png',
            'bg_auth' =>  '/media/musik/people.jpg',
            'banner' =>  '/media/music/enjoymusic.jpg',
            'pemerintah' =>  'Indonesia',
            'dinas' =>  'Konser Musik Indonesia',
            'alamat' =>  '',
            'telepon' =>  '',
            'email' =>  '',
        ]);
    }
}
