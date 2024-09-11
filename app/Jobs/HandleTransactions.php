<?php

namespace App\Jobs;

use App\Models\InventoryTransaction;
use App\Models\ProductInventory;
use App\Repositories\Products\ProductInventoryRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class HandleTransactions implements ShouldQueue
{
    use Queueable;

    public $tries = 3;



    public int $product_id;
    public int $vendor_id;


    public function backoff()
    {
        return [10, 30, 60];
    }

    /**
     * Create a new job instance.
     */
    public function __construct(int $product_id, int $vendor_id)
    {
        $this->product_id = $product_id;
        $this->vendor_id = $vendor_id;
    }

    /**
     * Execute the job.
     */

    public function handle(ProductInventoryRepository $product_inventory_repository): void
    {
        try {
            $quantity = InventoryTransaction::where(['product_id' => $this->product_id, 'vendor_id' => $this->vendor_id])->sum('quantity');
            Log::info(["Quantity" => $quantity]);
            $product_inventory_repository->calculateTransaction($this->product_id, $this->vendor_id, $quantity);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Exception $exception)
    {
        // Logic to handle job failure after max retry attempts
        Log::error('Job failed after max retries: ' . $exception->getMessage());
    }
}
