<?php

namespace App\Repositories;

use App\Models\Review;
use App\Models\Product;
use App\Models\User;
use App\Repositories\Interfaces\ReviewRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ReviewRepository implements ReviewRepositoryInterface
{
    /**
     * @var Review
     */
    protected $review;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var User
     */
    protected $user;

    /**
     * ReviewRepository constructor.
     *
     * @param Review $review
     * @param Product $product
     * @param User $user
     */
    public function __construct(Review $review, Product $product, User $user)
    {
        $this->review = $review;
        $this->product = $product;
        $this->user = $user;
    }

    /**
     * Get all reviews with their relationships (user and product)
     * with optional filtering and pagination
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getAllWithRelations(Request $request): LengthAwarePaginator
    {
        $query = $this->review->with(['user', 'product']);

        // Filter by rating if specified
        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Sort by created_at desc by default
        $sortField = $request->sort_by ?? 'created_at';
        $sortOrder = $request->sort_order ?? 'desc';
        $query->orderBy($sortField, $sortOrder);

        // Paginate results
        $perPage = $request->per_page ?? 15;

        return $query->paginate($perPage);
    }

    /**
     * Find review by id with relationships
     *
     * @param int $id
     * @return Review|null
     */
    public function findByIdWithRelations(int $id): ?Review
    {
        return $this->review->with(['user', 'product'])->find($id);
    }

    /**
     * Find review by id
     *
     * @param int $id
     * @return Review|null
     */
    public function findById(int $id): ?Review
    {
        return $this->review->find($id);
    }

    /**
     * Find review by id or fail
     *
     * @param int $id
     * @return Review
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int $id): Review
    {
        return $this->review->findOrFail($id);
    }

    /**
     * Create new review
     *
     * @param array $data
     * @return Review
     */
    public function create(array $data): Review
    {
        $review = $this->review->create($data);
        return $this->findByIdWithRelations($review->review_id);
    }

    /**
     * Update review
     *
     * @param int $id
     * @param array $data
     * @return Review|null
     */
    public function update(int $id, array $data): ?Review
    {
        $review = $this->findById($id);

        if (!$review) {
            return null;
        }

        $review->update($data);
        return $this->findByIdWithRelations($id);
    }

    /**
     * Delete review
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $review = $this->findById($id);

        if (!$review) {
            return false;
        }

        return $review->delete();
    }

    /**
     * Get all reviews for a specific product with pagination
     *
     * @param int $productId
     * @param Request|null $request
     * @return LengthAwarePaginator
     */
    public function getByProductId(int $productId, Request $request = null): LengthAwarePaginator
    {
        $request = $request ?? new Request();
        $query = $this->review->with(['user'])
            ->where('product_id', $productId);

        // Filter by rating if specified
        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }

        // Sort by created_at desc by default
        $sortField = $request->sort_by ?? 'created_at';
        $sortOrder = $request->sort_order ?? 'desc';
        $query->orderBy($sortField, $sortOrder);

        // Paginate results
        $perPage = $request->per_page ?? 10;

        return $query->paginate($perPage);
    }

    /**
     * Get all reviews by a specific user with pagination
     *
     * @param int $userId
     * @param Request|null $request
     * @return LengthAwarePaginator
     */
    public function getByUserId(int $userId, Request $request = null): LengthAwarePaginator
    {
        $request = $request ?? new Request();
        $query = $this->review->with(['product'])
            ->where('user_id', $userId);

        // Filter by rating if specified
        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }

        // Sort by created_at desc by default
        $sortField = $request->sort_by ?? 'created_at';
        $sortOrder = $request->sort_order ?? 'desc';
        $query->orderBy($sortField, $sortOrder);

        // Paginate results
        $perPage = $request->per_page ?? 10;

        return $query->paginate($perPage);
    }

    /**
     * Calculate average rating for a product
     *
     * @param int $productId
     * @return float
     */
    public function getAverageRatingForProduct(int $productId): float
    {
        return $this->review->where('product_id', $productId)
            ->avg('rating') ?? 0.0;
    }

    /**
     * Check if user has already reviewed the product
     *
     * @param int $userId
     * @param int $productId
     * @return bool
     */
    public function userHasReviewedProduct(int $userId, int $productId): bool
    {
        return $this->review->where([
            'user_id' => $userId,
            'product_id' => $productId
        ])->exists();
    }

    /**
     * Get review by user and product
     *
     * @param int $userId
     * @param int $productId
     * @return Review|null
     */
    public function getByUserAndProduct(int $userId, int $productId): ?Review
    {
        return $this->review->where([
            'user_id' => $userId,
            'product_id' => $productId
        ])->first();
    }
}
