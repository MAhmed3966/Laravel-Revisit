<?php

namespace Database\Seeders;

use App\Models\ImageSettings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ImageSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sizes = ['small' => [300, 200], 'medium' => [600, 500], 'large' => [1000, 900]];
        foreach($sizes as $key => $size)
        {
            ImageSettings::create([
                'size' => $key,
                'dimension' => json_encode(["width" => $sizes[$key][0],"height" => $sizes[$key][1]])
            ]);
        }
    }
}
