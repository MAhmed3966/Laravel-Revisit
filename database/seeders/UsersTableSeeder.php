<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i < 50; $i++) {
            DB::table('users')->insert([
                'name' => str::random(10),
                'email' => Str::random(10) . '@example.com',
                'password' => Hash::make('password'),
            ]);
        }
    }
}
