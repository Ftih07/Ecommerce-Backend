<?php

namespace App\Http\Controllers\API;

use App\Models\Review;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\ReviewRepositoryInterface;

class ReviewController extends Controller
{
    /**
     * @var \App\Repositories\Interfaces\ReviewRepositoryInterface
     */
    protected $reviewRepository;

    /**
     * ReviewController constructor.
     *
     * @param \App\Repositories\Interfaces\ReviewRepositoryInterface $reviewRepository
     */
    public function __construct(ReviewRepositoryInterface $reviewRepository)
    {
        $this->reviewRepository = $reviewRepository;
    }
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
        $reviews = $this->reviewRepository->getAllWithRelations();
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

        $review = $this->reviewRepository->create($request->all());

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
        $review = $this->reviewRepository->findByIdWithRelations($id);
        if (!$review) {
            return response()->json(['message' => 'Review not found'], 404);
        }
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
        $request->validate([
            'user_id' => 'sometimes|exists:users,user_id',
            'product_id' => 'sometimes|exists:products,product_id',
            'rating' => 'sometimes|integer|min:1|max:5', // Pakai `sometimes` agar tidak wajib saat update
            'review' => 'sometimes|string',
        ]);

        $review = $this->reviewRepository->update($id, $request->all());

        if (!$review) {
            return response()->json(['message' => 'Review not found'], 404);
        }

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
        $deleted = $this->reviewRepository->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Review not found'], 404);
        }

        return response()->json(['message' => 'Review deleted successfully'], 200);
    }

    /**
     * @OA\Get(
     *     path="/products/{product_id}/reviews",
     *     summary="Get reviews for a specific product",
     *     description="Mengambil semua review untuk produk tertentu",
     *     operationId="getProductReviews",
     *     tags={"Reviews"},
     *     @OA\Parameter(
     *         name="product_id",
     *         in="path",
     *         required=true,
     *         description="ID produk",
     *         @OA\Schema(type="integer", example=1)
     *     ),
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
    public function getProductReviews($productId)
    {
        $reviews = $this->reviewRepository->getByProductId($productId);
        return response()->json($reviews, 200);
    }

    /**
     * @OA\Get(
     *     path="/users/{user_id}/reviews",
     *     summary="Get reviews by a specific user",
     *     description="Mengambil semua review yang dibuat oleh user tertentu",
     *     operationId="getUserReviews",
     *     tags={"Reviews"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="ID user",
     *         @OA\Schema(type="integer", example=1)
     *     ),
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
    public function getUserReviews($userId)
    {
        $reviews = $this->reviewRepository->getByUserId($userId);
        return response()->json($reviews, 200);
    }
}
