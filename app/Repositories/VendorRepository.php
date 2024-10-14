<?php
namespace App\Repositories\Products;

use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\Vendor;
use App\Repositories\Products\BaseRepository;


class VendorRepository extends BaseRepository
{
    public function __construct(Vendor $vendor){
            parent::__construct($vendor);
        }


}
