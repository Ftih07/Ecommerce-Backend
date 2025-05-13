<?php

namespace App\Repositories\Interfaces;

interface ProductImageRepositoryInterface
{
    /**
     * Get all product images
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll();

    /**
     * Find product image by id
     *
     * @param int $id
     * @return \App\Models\ProductImage|null
     */
    public function findById($id);

    /**
     * Create new product image
     *
     * @param array $data
     * @return \App\Models\ProductImage
     */
    public function create(array $data);

    /**
     * Update product image
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\ProductImage|null
     */
    public function update($id, array $data);

    /**
     * Delete product image
     *
     * @param int $id
     * @return bool
     */
    public function delete($id);

    /**
     * Get all images for a specific product
     *
     * @param int $productId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByProductId($productId);
}
