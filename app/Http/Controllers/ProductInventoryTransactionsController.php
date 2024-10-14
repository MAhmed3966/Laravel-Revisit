<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInventoryRequest;
use App\Http\Resources\InventoryTransactionResource;
use App\Jobs\HandleTransactions;
use App\Repositories\Products\ProductInventoryTransactionRepository;
use Illuminate\Http\Request;

class ProductInventoryTransactionsController extends BaseController
{
    public $product_inventory;
    public function __construct(ProductInventoryTransactionRepository $product_inventory)
    {
        $this->product_inventory = $product_inventory;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $transactions = $this->product_inventory->index();
            if (count($transactions) < 0) {
                return $this->errorResponse("No Transaction Made", "", 404);
            }
            return InventoryTransactionResource::collection($transactions);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), '', 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInventoryRequest $request)
    {
        try {
            $is_inserted = $this->product_inventory->store($request->all());
            if ($is_inserted) {
                HandleTransactions::dispatch($request->product_id, $request->vendor_id);
                return $this->successResponse("", "Transaction stored successfully");
            } else {
        return $this->errorResponse("", "Transaction not stored", 500);
            }
        } catch (\Exception $e) {
            return $this->errorResponse("", $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
