<?php

namespace App\Repositories;

use App\Models\Review;
use App\Repositories\Interfaces\ReviewRepositoryInterface;

class ReviewRepository implements ReviewRepositoryInterface
{
    /**
     * @var \App\Models\Review
     */
    protected $model;

    /**
     * ReviewRepository constructor.
     *
     * @param \App\Models\Review $review
     */
    public function __construct(Review $review)
    {
        $this->model = $review;
    }

    /**
     * Get all reviews with their relationships (user and product)
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWithRelations()
    {
        return $this->model->with(['user', 'product'])->get();
    }

    /**
     * Find review by id with relationships
     *
     * @param int $id
     * @return \App\Models\Review|null
     */
    public function findByIdWithRelations($id)
    {
        return $this->model->with(['user', 'product'])->find($id);
    }

    /**
     * Find review by id
     *
     * @param int $id
     * @return \App\Models\Review|null
     */
    public function findById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Create new review
     *
     * @param array $data
     * @return \App\Models\Review
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update review
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\Review|null
     */
    public function update($id, array $data)
    {
        $review = $this->findById($id);

        if (!$review) {
            return null;
        }

        $review->update($data);
        return $review;
    }

    /**
     * Delete review
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $review = $this->findById($id);

        if (!$review) {
            return false;
        }

        return $review->delete();
    }

    /**
     * Get all reviews for a specific product
     *
     * @param int $productId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByProductId($productId)
    {
        return $this->model->with(['user'])->where('product_id', $productId)->get();
    }

    /**
     * Get all reviews by a specific user
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByUserId($userId)
    {
        return $this->model->with(['product'])->where('user_id', $userId)->get();
    }
}
