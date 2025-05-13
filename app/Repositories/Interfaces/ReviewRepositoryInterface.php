<?php

namespace App\Repositories\Interfaces;

use App\Models\Review;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface ReviewRepositoryInterface
{
    /**
     * Get all reviews with their relationships (user and product)
     * with optional filtering and pagination
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getAllWithRelations(Request $request): LengthAwarePaginator;

    /**
     * Find review by id with relationships
     *
     * @param int $id
     * @return Review|null
     */
    public function findByIdWithRelations(int $id): ?Review;

    /**
     * Find review by id
     *
     * @param int $id
     * @return Review|null
     */
    public function findById(int $id): ?Review;

    /**
     * Find review by id or fail
     *
     * @param int $id
     * @return Review
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int $id): Review;

    /**
     * Create new review
     *
     * @param array $data
     * @return Review
     */
    public function create(array $data): Review;

    /**
     * Update review
     *
     * @param int $id
     * @param array $data
     * @return Review|null
     */
    public function update(int $id, array $data): ?Review;

    /**
     * Delete review
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Get all reviews for a specific product with pagination
     *
     * @param int $productId
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getByProductId(int $productId, Request $request = null): LengthAwarePaginator;

    /**
     * Get all reviews by a specific user with pagination
     *
     * @param int $userId
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getByUserId(int $userId, Request $request = null): LengthAwarePaginator;

    /**
     * Calculate average rating for a product
     *
     * @param int $productId
     * @return float
     */
    public function getAverageRatingForProduct(int $productId): float;

    /**
     * Check if user has already reviewed the product
     *
     * @param int $userId
     * @param int $productId
     * @return bool
     */
    public function userHasReviewedProduct(int $userId, int $productId): bool;

    /**
     * Get review by user and product
     *
     * @param int $userId
     * @param int $productId
     * @return Review|null
     */
    public function getByUserAndProduct(int $userId, int $productId): ?Review;
}
