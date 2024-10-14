<?php
namespace App\Repositories\Products;

use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Repositories\Products\BaseRepository;


class ProductInventoryTransactionRepository extends BaseRepository
{
    public function __construct(InventoryTransaction $inventoryTransaction){
            parent::__construct($inventoryTransaction);
        }


}
