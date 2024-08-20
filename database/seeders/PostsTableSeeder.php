<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PostsTableSeeder extends Seeder
{
    public function run()
    {
        for ($i = 1; $i < 50; $i++) {
            DB::table('posts')->insert([
                'title' => Str::random(10),
                'content' => Str::random(50),
                'user_id' => $i, // Assuming user with ID 1 exists
            ]);
        }
    }
}
