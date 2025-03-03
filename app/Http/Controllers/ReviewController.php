<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        // Contoh: load user & product
        $reviews = Review::with(['user', 'product'])->get();
        return response()->json($reviews, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'product_id' => 'required|exists:products,product_id',
            'content' => 'nullable|string',
        ]);

        $review = Review::create($request->all());
        return response()->json($review, 201);
    }

    public function show($id)
    {
        $review = Review::with(['user', 'product'])->find($id);
        if (!$review) {
            return response()->json(['message' => 'Review not found'], 404);
        }
        return response()->json($review, 200);
    }

    public function update(Request $request, $id)
    {
        $review = Review::find($id);
        if (!$review) {
            return response()->json(['message' => 'Review not found'], 404);
        }

        $request->validate([
            'content' => 'string',
            'user_id' => 'exists:users,user_id',
            'product_id' => 'exists:products,product_id',
        ]);

        $review->update($request->all());
        return response()->json($review, 200);
    }

    public function destroy($id)
    {
        $review = Review::find($id);
        if (!$review) {
            return response()->json(['message' => 'Review not found'], 404);
        }
        $review->delete();
        return response()->json(['message' => 'Review deleted'], 200);
    }
}
