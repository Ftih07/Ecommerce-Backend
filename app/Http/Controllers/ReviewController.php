<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // GET /reviews
    public function index()
    {
        $reviews = Review::with(['user', 'product'])->get();
        return response()->json($reviews, 200);
    }

    // POST /reviews
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'product_id' => 'required|exists:products,product_id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string',
        ]);

        $review = Review::create($request->all());

        return response()->json([
            'message' => 'Review created successfully',
            'review' => $review
        ], 201);
    }

    // GET /reviews/{id}
    public function show($id)
    {
        $review = Review::with(['user', 'product'])->findOrFail($id);
        return response()->json($review, 200);
    }

    // PUT/PATCH /reviews/{id}
    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        $request->validate([
            'user_id' => 'sometimes|exists:users,user_id',
            'product_id' => 'sometimes|exists:products,product_id',
            'rating' => 'sometimes|integer|min:1|max:5', // Pakai `sometimes` agar tidak wajib saat update
            'review' => 'sometimes|string',
        ]);

        $review->update($request->all());

        return response()->json([
            'message' => 'Review updated successfully',
            'review' => $review
        ], 200);
    }

    // DELETE /reviews/{id}
    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return response()->json(['message' => 'Review deleted successfully'], 200);
    }
}
