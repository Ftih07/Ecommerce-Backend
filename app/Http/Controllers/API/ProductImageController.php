<?php

namespace App\Http\Controllers\API;

use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductImageController extends Controller
{
    public function index()
    {
        return response()->json(ProductImage::all(), 200);
    }

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

    public function show($id)
    {
        $image = ProductImage::find($id);
        if (!$image) {
            return response()->json(['message' => 'ProductImage not found'], 404);
        }
        return response()->json($image, 200);
    }

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
