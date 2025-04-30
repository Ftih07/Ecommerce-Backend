<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index()
    {
        // Contoh: load store & category juga
        $products = Product::with(['store', 'category'])->get();
        return response()->json($products, 200);
    }

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
