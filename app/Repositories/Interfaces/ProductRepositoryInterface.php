<?php

namespace App\Repositories\Interfaces;

interface ProductRepositoryInterface
{
    /**
     * Get all products with their relationships
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWithRelations();

    /**
     * Get all products
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll();

    /**
     * Find product by id with relationships
     *
     * @param int $id
     * @return \App\Models\Product|null
     */
    public function findByIdWithRelations($id);

    /**
     * Find product by id
     *
     * @param int $id
     * @return \App\Models\Product|null
     */
    public function findById($id);

    /**
     * Create new product
     *
     * @param array $data
     * @return \App\Models\Product
     */
    public function create(array $data);

    /**
     * Update product
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\Product|null
     */
    public function update($id, array $data);

    /**
     * Delete product
     *
     * @param int $id
     * @return bool
     */
    public function delete($id);

    /**
     * Get all products for a specific category
     *
     * @param int $categoryId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByCategoryId($categoryId);

    /**
     * Get all products for a specific store
     *
     * @param int $storeId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByStoreId($storeId);
}
