<?php

namespace App\Http\Controllers;

use App\Http\Resources\VendorResource;
use App\Models\Vendor;
use App\Repositories\Products\VendorRepository;
use Illuminate\Http\Request;

class VendorController extends BaseController
{
    public $vendor_repository;
    public function __construct(VendorRepository $vendor_repository)
    {
        $this->vendor_repository =  $vendor_repository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $vendors =  Vendor::all();
            return VendorResource::collection($vendors);
        } catch(\Exception $e){
            return $this->errorResponse($e->getMessage(),"" , 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $is_inserted = $this->vendor_repository->store($request->all());
            if ($is_inserted) {
                return $this->successResponse(null, "Product Created");
            } else {
                return $this->errorResponse("Product Not Created","",500);
            }
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(),"",500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $is_updated = $this->vendor_repository->update($id, $request->all());
            if ($is_updated) {
                return $this->successResponse(null, "Vendor updated");

            } else {
                return $this->errorResponse("Vendor Not updated","",500);
            }
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(),"",500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $is_updated = $this->vendor_repository->delete($id);
            if ($is_updated) {
                return $this->successResponse("", "Product deleted");
            } else {
                return $this->errorResponse("Product Not deleted","",500);
            }
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(),"",500);
        }
    }
}
