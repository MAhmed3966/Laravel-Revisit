<?php

namespace App\Http\Controllers;

use App\Http\Requests\Products\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Repositories\Products\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    protected $productRepo;
    public $model;
    public function __construct(ProductRepository $productRepo)
    {
        $this->productRepo = $productRepo;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $products = $this->productRepo->index();
            return ProductResource::collection($products);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $is_inserted = $this->productRepo->store($request->all());
            if ($is_inserted) {
                return response()->json(['message' => 'Product Created', "error" => false], 200);
            } else {
                return response()->json(['message' => 'Product Not Created', "error" => true], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request)
    {
        try {
            $is_updated = $this->productRepo->update($request->id, $request->all());
            if ($is_updated) {
                return response()->json(['message' => 'Product updated', "error" => false], 200);
            } else {
                return response()->json(['message' => 'Product Not updated', "error" => true], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($product)
    {
        try {
            $is_updated = $this->productRepo->delete($product);
            if ($is_updated) {
                return response()->json(['message' => 'Product deleted', "error" => false], 200);
            } else {
                return response()->json(['message' => 'Product Not deleted', "error" => true], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
