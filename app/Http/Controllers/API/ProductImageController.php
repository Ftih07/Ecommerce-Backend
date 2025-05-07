<?php

namespace App\Http\Controllers\API;

use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * @OA\Tag(
 *     name="Product Images",
 * )
 */

class ProductImageController extends Controller
{
    /**
 * @OA\Get(
 *     path="/product-images",
 *     summary="Get list of all product images",
 *     tags={"Product Images"},
 *     @OA\Response(
 *         response=200,
 *         description="List of product images",
 *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/ProductImage"))
 *     )
 * )
 */

    public function index()
    {
        return response()->json(ProductImage::all(), 200);
    }
/**
 * @OA\Post(
 *     path="/product-images",
 *     summary="Create a new product image",
 *     tags={"Product Images"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "product_id", "path"},
 *             @OA\Property(property="name", type="string", example="image1.jpg"),
 *             @OA\Property(property="product_id", type="integer", example=1),
 *             @OA\Property(property="path", type="string", example="uploads/images/image1.jpg")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Product image created",
 *         @OA\JsonContent(ref="#/components/schemas/ProductImage")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     )
 * )
 */

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'product_id' => 'required|exists:products,product_id',
            'path' => 'string',
        ]);

        $image = ProductImage::create($request->all());
        return response()->json($image, 201);
    }
/**
 * @OA\Get(
 *     path="/product-images/{id}",
 *     summary="Get a specific product image by ID",
 *     tags={"Product Images"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Product image ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product image data",
 *         @OA\JsonContent(ref="#/components/schemas/ProductImage")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product image not found"
 *     )
 * )
 */

    public function show($id)
    {
        $image = ProductImage::find($id);
        if (!$image) {
            return response()->json(['message' => 'ProductImage not found'], 404);
        }
        return response()->json($image, 200);
    }
/**
 * @OA\Put(
 *     path="/product-images/{id}",
 *     summary="Update a product image",
 *     tags={"Product Images"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Product image ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="updated_image.jpg"),
 *             @OA\Property(property="product_id", type="integer", example=1),
 *             @OA\Property(property="path", type="string", example="uploads/updated_image.jpg")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product image updated",
 *         @OA\JsonContent(ref="#/components/schemas/ProductImage")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product image not found"
 *     )
 * )
 */

    public function update(Request $request, $id)
    {
        $image = ProductImage::find($id);
        if (!$image) {
            return response()->json(['message' => 'ProductImage not found'], 404);
        }

        $request->validate([
            'name' => 'string',
            'product_id' => 'exists:products,product_id',
            'path' => 'string',
        ]);

        $image->update($request->all());
        return response()->json($image, 200);
    }
/**
 * @OA\Delete(
 *     path="/product-images/{id}",
 *     summary="Delete a product image",
 *     tags={"Product Images"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Product image ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product image deleted",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="ProductImage deleted")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product image not found"
 *     )
 * )
 */

    public function destroy($id)
    {
        $image = ProductImage::find($id);
        if (!$image) {
            return response()->json(['message' => 'ProductImage not found'], 404);
        }
        $image->delete();
        return response()->json(['message' => 'ProductImage deleted'], 200);
    }
}
