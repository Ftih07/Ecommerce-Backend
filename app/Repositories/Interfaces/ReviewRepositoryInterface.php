<?php

namespace App\Repositories\Interfaces;

interface ReviewRepositoryInterface
{
    /**
     * Get all reviews with their relationships (user and product)
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWithRelations();

    /**
     * Find review by id with relationships
     *
     * @param int $id
     * @return \App\Models\Review|null
     */
    public function findByIdWithRelations($id);

    /**
     * Find review by id
     *
     * @param int $id
     * @return \App\Models\Review|null
     */
    public function findById($id);

    /**
     * Create new review
     *
     * @param array $data
     * @return \App\Models\Review
     */
    public function create(array $data);

    /**
     * Update review
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\Review|null
     */
    public function update($id, array $data);

    /**
     * Delete review
     *
     * @param int $id
     * @return bool
     */
    public function delete($id);

    /**
     * Get all reviews for a specific product
     *
     * @param int $productId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByProductId($productId);

    /**
     * Get all reviews by a specific user
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByUserId($userId);
}
