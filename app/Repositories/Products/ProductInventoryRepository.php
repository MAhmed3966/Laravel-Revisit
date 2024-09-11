<?php

namespace App\Repositories\Products;

use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\ProductInventory;
use App\Repositories\Products\BaseRepository;
use Exception;
use Illuminate\Support\Facades\Log;

class ProductInventoryRepository extends BaseRepository
{
    public function __construct(ProductInventory $product_inventory)
    {
        parent::__construct($product_inventory);
    }

    /**
     * Method calculateTransaction
     *
     * @param $product_id $product_id [explicite description]
     * @param $vendor_id $vendor_id [explicite description]
     * @param $quantity $quantity [explicite description]
     *
     * @return void
     */
    public function calculateTransaction($product_id, $vendor_id, $quantity)
    {
        try {

            $inventory = ProductInventory::updateOrCreate(
                ['product_id' => $product_id, 'vendor_id' => $vendor_id], // Correct format for matching columns
                ['product_id' => $product_id, 'vendor_id' => $vendor_id, 'total_quantity' => $quantity] // Values to insert/update
            );
            if($inventory){
                return true;
            }
            return false;
            // throw new Exception("Unable to update the product Inventory");

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            throw new Exception("Unable to update the product Inventory");
        }
    }
}
