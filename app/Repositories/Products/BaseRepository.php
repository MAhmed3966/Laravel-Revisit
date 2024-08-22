<?php

namespace App\Repositories\Products;

use App\Models\Product;
use App\Services\Products\BaseInterface;
use App\Services\Products\ProductService;
use Illuminate\Support\Facades\Log;

class BaseRepository implements BaseInterface
{
    protected $model;
    public function __construct($model)
    {
        $this->model = $model;
    }

    public function index()
    {
        try {
            return $this->model::all();
        } catch (\Exception $e) {
            Log::error('Failed to retrieve products: ' . $e->getMessage());
            throw new \RuntimeException("Unable to fetch the products at this time");
        }
    }
    public function store($data)
    {
        try {
            if ($this->model->create($data)) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage() . "  Enable to create the Product");
            throw new \Exception("Unable to create the product");
        }
    }
    public function update($id,$request)
    {
        try {
            $is_updated = $this->model->where('id', $id)->update($request);
            if ($is_updated) {
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error($e->getMessage() . "");
            throw new \RuntimeException("Record not updated");
        }
    }
    public function delete($id) {
        try {
            $is_deleted = $this->model->where('id', $id)->delete();
            if ($is_deleted) {
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error($e->getMessage() . "");
            throw new \RuntimeException("Record not updated");
        }
    }
}
