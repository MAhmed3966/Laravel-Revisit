<?php

namespace Database\Seeders;

use App\Models\InventoryTransaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InventoryTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        try {
            for ($i = 0; $i < 50; $i++) {
                InventoryTransaction::create([
                    'quantity' => rand(-1000, 1000),
                    'vendor_id' =>  1,
                    'product_id' => 1,
                    'status' => 0,
                ]);
            }
        } catch (\Exception $e) {
            dd(''. $e->getMessage());
        }
    }
}
