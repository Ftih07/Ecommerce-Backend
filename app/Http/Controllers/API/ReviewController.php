<?php

namespace App\Http\Controllers\API;

use App\Models\Review;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\ReviewRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReviewController extends Controller
{
    /**
     * @var ReviewRepositoryInterface
     */
    protected $reviewRepository;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * ReviewController constructor.
     *
     * @param ReviewRepositoryInterface $reviewRepository
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        ReviewRepositoryInterface $reviewRepository,
        ProductRepositoryInterface $productRepository
    ) {
        $this->reviewRepository = $reviewRepository;
        $this->productRepository = $productRepository;
    }
    /**
     * @OA\Get(
     *     path="/reviews",
     *     summary="Get all reviews",
     *     description="Mengambil semua review beserta data user dan produk terkait dengan pagination dan filtering",
     *     operationId="getReviews",
     *     tags={"Reviews"},
     *     @OA\Parameter(
     *         name="rating",
     *         in="query",
     *         description="Filter by rating (1-5)",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=5)
     *     ),
     *     @OA\Parameter(
     *         name="from_date",
     *         in="query",
     *         description="Filter reviews from this date (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="to_date",
     *         in="query",
     *         description="Filter reviews until this date (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Field to sort by",
     *         required=false,
     *         @OA\Schema(type="string", enum={"created_at", "rating"})
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         description="Sort direction",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mengambil data review",
     *         @OA\JsonContent(
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Review")),
     *             @OA\Property(property="first_page_url", type="string"),
     *             @OA\Property(property="from", type="integer"),
     *             @OA\Property(property="last_page", type="integer"),
     *             @OA\Property(property="last_page_url", type="string"),
     *             @OA\Property(property="links", type="array", @OA\Items(
     *                 @OA\Property(property="url", type="string", nullable=true),
     *                 @OA\Property(property="label", type="string"),
     *                 @OA\Property(property="active", type="boolean")
     *             )),
     *             @OA\Property(property="next_page_url", type="string", nullable=true),
     *             @OA\Property(property="path", type="string"),
     *             @OA\Property(property="per_page", type="integer"),
     *             @OA\Property(property="prev_page_url", type="string", nullable=true),
     *             @OA\Property(property="to", type="integer"),
     *             @OA\Property(property="total", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Terjadi kesalahan server"
     *     )
     * )
     */
    // GET /reviews
    public function index(Request $request)
    {
        try {
            $reviews = $this->reviewRepository->getAllWithRelations($request);
            return response()->json($reviews, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving reviews',
                'error' => $e->getMessage()
            ], 500);
        }
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
     *         response=409,
     *         description="User already reviewed this product"
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
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer|exists:users,user_id',
                'product_id' => 'required|integer|exists:products,product_id',
                'rating' => 'required|integer|min:1|max:5',
                'review' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            $userId = $request->user_id;
            $productId = $request->product_id;

            // Check if user has already reviewed this product
            if ($this->reviewRepository->userHasReviewedProduct($userId, $productId)) {
                return response()->json([
                    'message' => 'User has already reviewed this product. Please update the existing review instead.'
                ], 409);
            }

            // Create data array
            $data = $request->only(['user_id', 'product_id', 'rating', 'review']);

            $review = $this->reviewRepository->create($data);

            return response()->json([
                'message' => 'Review created successfully',
                'review' => $review
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating review',
                'error' => $e->getMessage()
            ], 500);
        }
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
     *         response=400,
     *         description="Request tidak valid"
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
        try {
            // Find the review first
            $review = $this->reviewRepository->findById($id);

            if (!$review) {
                return response()->json(['message' => 'Review not found'], 404);
            }

            // Validate the request data
            $validator = Validator::make($request->all(), [
                'rating' => 'sometimes|required|integer|min:1|max:5',
                'review' => 'sometimes|nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Only update rating and review fields
            $data = $request->only(['rating', 'review']);

            $updatedReview = $this->reviewRepository->update($id, $data);

            return response()->json([
                'message' => 'Review updated successfully',
                'review' => $updatedReview
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating review',
                'error' => $e->getMessage()
            ], 500);
        }
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
        try {
            // Find the review first
            $review = $this->reviewRepository->findById($id);

            if (!$review) {
                return response()->json(['message' => 'Review not found'], 404);
            }

            $deleted = $this->reviewRepository->delete($id);

            return response()->json(['message' => 'Review deleted successfully'], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting review',
                'error' => $e->getMessage()
            ], 500);
        }
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
    public function getProductReviews($productId, Request $request)
    {
        try {
            $reviews = $this->reviewRepository->getByProductId($productId, $request);
            $avgRating = $this->reviewRepository->getAverageRatingForProduct($productId);

            return response()->json([
                'average_rating' => round($avgRating, 1),
                'reviews' => $reviews
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving product reviews',
                'error' => $e->getMessage()
            ], 500);
        }
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
     *     @OA\Parameter(
     *         name="rating",
     *         in="query",
     *         description="Filter by rating (1-5)",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=5)
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Field to sort by",
     *         required=false,
     *         @OA\Schema(type="string", enum={"created_at", "rating"})
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         description="Sort direction",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mengambil data review",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Review"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Terjadi kesalahan server"
     *     )
     * )
     */
    public function getUserReviews($userId, Request $request)
    {
        try {
            $reviews = $this->reviewRepository->getByUserId($userId, $request);
            return response()->json($reviews, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving user reviews',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
