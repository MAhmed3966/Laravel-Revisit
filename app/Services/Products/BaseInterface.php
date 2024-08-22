<?php
namespace App\Services\Products;

use App\Models\Product;


interface BaseInterface
{
    public function index();
    public function store($data);
    public function update($id, $request);
    public function delete($id);


}
