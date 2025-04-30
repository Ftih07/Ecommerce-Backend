<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * @OA\Get(
     *     path="/reviews",
     *     summary="Get all reviews",
     *     description="Mengambil semua review beserta data user dan produk terkait",
     *     operationId="getReviews",
     *     tags={"Reviews"},
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mengambil data review",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Review"))
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Terjadi kesalahan server"
     *     )
     * )
     */ 
    // GET /reviews
    public function index()
    {
        $reviews = Review::with(['user', 'product'])->get();
        return response()->json($reviews, 200);
    }

    /**
     * @OA\Post(
     *     path="/reviews",
     *     summary="Create a review",
     *     description="Membuat review baru untuk produk",
     *     operationId="createReview",
     *     tags={"Reviews"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "product_id", "rating"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="product_id", type="integer", example=3),
     *             @OA\Property(property="rating", type="integer", example=4),
     *             @OA\Property(property="review", type="string", example="Produk bagus, sangat puas!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Review berhasil dibuat",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Review created successfully"),
     *             @OA\Property(property="review", ref="#/components/schemas/Review")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Request tidak valid"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Terjadi kesalahan server"
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/reviews/{id}",
     *     summary="Get a review by ID",
     *     description="Mengambil review berdasarkan ID",
     *     operationId="getReviewById",
     *     tags={"Reviews"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID review",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mengambil review",
     *         @OA\JsonContent(ref="#/components/schemas/Review")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Review tidak ditemukan"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Terjadi kesalahan server"
     *     )
     * )
     */
    // GET /reviews/{id}
    public function show($id)
    {
        $review = Review::with(['user', 'product'])->findOrFail($id);
        return response()->json($review, 200);
    }

    /**
     * @OA\Put(
     *     path="/reviews/{id}",
     *     summary="Update a review",
     *     description="Memperbarui review berdasarkan ID",
     *     operationId="updateReview",
     *     tags={"Reviews"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID review",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="integer", example=2),
     *             @OA\Property(property="product_id", type="integer", example=4),
     *             @OA\Property(property="rating", type="integer", example=5),
     *             @OA\Property(property="review", type="string", example="Produk semakin baik!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Review berhasil diperbarui",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Review updated successfully"),
     *             @OA\Property(property="review", ref="#/components/schemas/Review")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Review tidak ditemukan"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Terjadi kesalahan server"
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/reviews/{id}",
     *     summary="Delete a review",
     *     description="Menghapus review berdasarkan ID",
     *     operationId="deleteReview",
     *     tags={"Reviews"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID review",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Review berhasil dihapus",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Review deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Review tidak ditemukan"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Terjadi kesalahan server"
     *     )
     * )
     */
    // DELETE /reviews/{id}
    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return response()->json(['message' => 'Review deleted successfully'], 200);
    }
}
