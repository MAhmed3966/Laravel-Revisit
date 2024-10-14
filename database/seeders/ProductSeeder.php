<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;


class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for( $i = 0; $i < 50; $i++ ){
            Product::create([
            'title' => Str::random(10),
            'description' => Str::random(50),
            'sku' => Str::random(10),
            ]);
        }
    }
}
