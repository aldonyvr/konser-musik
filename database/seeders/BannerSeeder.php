<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Banner::create([
            'image' => '/media/musik/rockmusic.jpg',
        ]);
        Banner::create([
            'image' => '/media/musik/eventjazz.jpg',
        ]);
        Banner::create([
            'image' => '/media/musik/festval.jpg',
        ]);
        Banner::create([
            'image' => '/media/musik/event.jpg',
        ]);
    }
}
