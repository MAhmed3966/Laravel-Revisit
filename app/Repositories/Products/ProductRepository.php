<?php
namespace App\Repositories\Products;
use App\Models\Product;
use App\Repositories\Products\BaseRepository;


class ProductRepository extends BaseRepository
{
    public function __construct(Product $product){
        parent::__construct($product);
    }


}
