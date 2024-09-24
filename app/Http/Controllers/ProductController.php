<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteProductRequest;
use App\Http\Requests\Products\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Repositories\ImageRepository;
use App\Repositories\Products\ProductRepository;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    protected $productRepo;
    public $model;

    public $image_repo;

    public function __construct(ProductRepository $productRepo, ImageRepository $image_repo)
    {
        $this->productRepo = $productRepo;
        $this->image_repo =  $image_repo;
    }

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="View Products",
     *     description="View products",
     *     tags={"View Products"},
     *     @OA\Response(
     *         response=200,
     *         description="Collection of products",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="title", type="string", example="JlCE9I2nX8"),
     *                     @OA\Property(property="sku", type="string", example="yieQ9qzTGo"),
     *                     @OA\Property(property="description", type="string", example="pv1TL8RLfIlvDuzfAKRyqQLNRbU1hdHIt6AiDLbkTQjBpByjYT")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No Product found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="No Product found"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="An unexpected error occurred"
     *             )
     *         )
     *     )
     * )
     */

    public function index()
    {
        try {
            $products = $this->productRepo->index();
            if (count($products) === 0) {
                return $this->errorResponse("No Product Found","",404);
                // return response()->json(['error' => "No Content"], 204);
            }
            return ProductResource::collection($products);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(),"",500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Create a Product",
     *     description="Create a Product",
     *     tags={"Create a Product"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "description", "sku"},
     *             @OA\Property(property="title", type="string", example="dadsdas  asdadas dasd"),
     *             @OA\Property(property="description", type="string", example="dasdasadsas dasdasdasdadasffasdrweadcasd  sdasdas"),
     *             @OA\Property(property="sku", type="string", example="12345678")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product Created",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Product created"
     *             ),
     *              @OA\Property(
     *                 property="error",
     *                 type="boolean",
     *                 example=false
     *             )
     *         )
     *
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Product Not Created",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Product not created"
     *             ),
     *              @OA\Property(
     *                 property="error",
     *                 type="boolean",
     *                 example=true
     *             )
     *         )
     *     ),
     *    @OA\Response(
     *         response=500,
     *         description="Error Message",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Error Message"
     *             ),
     *              @OA\Property(
     *                 property="error",
     *                 type="boolean",
     *                 example=true
     *             )
     *         )
     *     )
     * )
     */

    public function store(Request $request)
    {
        try {
            $product = $this->productRepo->store($request->all());
            if ($product) {
                if($request->has('image')){
                    $image = $this->image_repo->storeImages($request, $product);
                }
                return $this->successResponse(null, "Product Created");
            } else {
                return $this->errorResponse("Product Not Created","",500);
            }
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(),$e->getTraceAsString(),500);
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
    * @OA\Put(
    *     path="/api/products/{product}",
    *     summary="Update a Product",
    *     description="Update a Product",
    *     tags={"Update a Product"},
    *     @OA\Parameter(
    *         name="product",
    *         in="path",
    *         description="ID of the product to update",
    *         required=true,
    *         @OA\Schema(type="integer")
    *     ),
    *     @OA\RequestBody(
    *         required=true,
    *         description="Product details to be updated",
    *         @OA\JsonContent(
    *             required={"title", "description", "sku"},
    *             @OA\Property(property="title", type="string", example="Product title"),
    *             @OA\Property(property="description", type="string", example="Product description"),
    *             @OA\Property(property="sku", type="string", example="12345678")
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Product updated successfully",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="Product updated"),
    *             @OA\Property(property="error", type="boolean", example=false)
    *         )
    *     ),
    *     @OA\Response(
    *         response=422,
    *         description="Validation Error",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="Validation Error"),
    *             @OA\Property(property="error", type="boolean", example=true)
    *         )
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="Internal Server Error",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="Error Message"),
    *             @OA\Property(property="error", type="boolean", example=true)
    *         )
    *     )
    * )
 */


    public function update(UpdateProductRequest $request)
    {
        try {
            $is_updated = $this->productRepo->update($request->id, $request->all());
            if ($is_updated) {
                return $this->successResponse(null, "Product updated");

            } else {
                return $this->errorResponse("Product Not updated","",500);
            }
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(),"",500);
        }
    }



 /**

 * @OA\Delete(
 *     path="/api/products/{product}",
 *     summary="Delete a product",
 *     tags={"Delete Products"},
 *     security={{ "sanctum":{}}},
 *     @OA\Parameter(
 *         name="product",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\SecurityScheme(
 *         securityScheme="bearerAuth",
 *         type="http",
 *         scheme="bearer",
 *         bearerFormat="JWT",
 *         description="Enter your Bearer token in the format"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product deleted",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 example="true"
 *             ),
 *             @OA\Property(
 *                 property="data",
 *                 type="string",
 *                 example=""
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Product deleted"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation Error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 @OA\Property(
 *                     property="id",
 *                     type="array",
 *                     @OA\Items(type="string", example="The selected id is invalid.")
 *                 ),
 *                 @OA\Property(
 *                     property="name",
 *                     type="array",
 *                     @OA\Items(type="string", example="The name field is required.")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="Product not deleted"
 *             )
 *         )
 *     )
 * )
 */




    public function destroy(DeleteProductRequest $request)
    {
        try {
            $is_updated = $this->productRepo->delete($request->id);
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
