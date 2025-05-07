<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{

    /**
 * @OA\Get(
 *     path="/products",
 *     summary="Get list of all products",
 *     tags={"Products"},
 *     @OA\Response(
 *         response=200,
 *         description="Successful response with list of products",
 *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Product"))
 *     )
 * )
 */

    public function index()
    {
        // Contoh: load store & category juga
        $products = Product::with(['store', 'category'])->get();
        return response()->json($products, 200);
    } 

    /**
 * @OA\Post(
 *     path="/products",
 *     summary="Create a new product",
 *     tags={"Products"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "stock", "status", "price", "store_id", "category_id"},
 *             @OA\Property(property="name", type="string", example="Smartphone X200"),
 *             @OA\Property(property="thumbnail_image", type="string", example="images/thumb.jpg"),
 *             @OA\Property(property="stock", type="integer", example=100),
 *             @OA\Property(property="status", type="string", enum={"active", "inactive"}, example="active"),
 *             @OA\Property(property="description", type="string", example="A flagship smartphone"),
 *             @OA\Property(property="price", type="number", format="float", example=299.99),
 *             @OA\Property(property="store_id", type="integer", example=1),
 *             @OA\Property(property="category_id", type="integer", example=2),
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Product created successfully",
 *         @OA\JsonContent(ref="#/components/schemas/Product")
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
            'thumbnail_image' => 'nullable|string',
            'stock' => 'required|integer',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'store_id' => 'required|exists:stores,store_id',
            'category_id' => 'required|exists:categories,category_id',
        ]);

        $product = Product::create($request->all());
        return response()->json($product, 201);
    }

    /**
 * @OA\Get(
 *     path="/products/{id}",
 *     summary="Get a specific product by ID",
 *     tags={"Products"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Product ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful response with product data",
 *         @OA\JsonContent(ref="#/components/schemas/Product")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product not found"
 *     )
 * )
 */

    public function show($id)
    {
        // Contoh: load relasi
        $product = Product::with(['store', 'category', 'reviews', 'productImages'])
                          ->find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        return response()->json($product, 200);
    }

    /**
 * @OA\Put(
 *     path="/products/{id}",
 *     summary="Update an existing product",
 *     tags={"Products"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Product ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="Updated Product Name"),
 *             @OA\Property(property="thumbnail_image", type="string", example="updated_thumb.jpg"),
 *             @OA\Property(property="stock", type="integer", example=150),
 *             @OA\Property(property="status", type="string", enum={"active", "inactive"}, example="inactive"),
 *             @OA\Property(property="description", type="string", example="Updated description"),
 *             @OA\Property(property="price", type="number", format="float", example=249.99),
 *             @OA\Property(property="store_id", type="integer", example=1),
 *             @OA\Property(property="category_id", type="integer", example=3),
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product updated successfully",
 *         @OA\JsonContent(ref="#/components/schemas/Product")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product not found"
 *     )
 * )
 */

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $request->validate([
            'stock' => 'integer',
            'status' => 'in:active,inactive',
            'price' => 'numeric',
            'store_id' => 'exists:stores,store_id',
            'category_id' => 'exists:categories,category_id',
        ]);

        $product->update($request->all());
        return response()->json($product, 200);
    }

    /**
 * @OA\Delete(
 *     path="/products/{id}",
 *     summary="Delete a product",
 *     tags={"Products"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Product ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Product deleted")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product not found"
 *     )
 * )
 */

    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        $product->delete();
        return response()->json(['message' => 'Product deleted'], 200);
    }
}
