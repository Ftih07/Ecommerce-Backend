<?php

namespace App\Repositories;

use App\Models\ProductImage;
use App\Repositories\Interfaces\ProductImageRepositoryInterface;

class ProductImageRepository implements ProductImageRepositoryInterface
{
    /**
     * @var \App\Models\ProductImage
     */
    protected $model;

    /**
     * ProductImageRepository constructor.
     *
     * @param \App\Models\ProductImage $productImage
     */
    public function __construct(ProductImage $productImage)
    {
        $this->model = $productImage;
    }

    /**
     * Get all product images
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Find product image by id
     *
     * @param int $id
     * @return \App\Models\ProductImage|null
     */
    public function findById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Create new product image
     *
     * @param array $data
     * @return \App\Models\ProductImage
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update product image
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\ProductImage|null
     */
    public function update($id, array $data)
    {
        $productImage = $this->findById($id);

        if (!$productImage) {
            return null;
        }

        $productImage->update($data);
        return $productImage;
    }

    /**
     * Delete product image
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $productImage = $this->findById($id);

        if (!$productImage) {
            return false;
        }

        return $productImage->delete();
    }

    /**
     * Get all images for a specific product
     *
     * @param int $productId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByProductId($productId)
    {
        return $this->model->where('product_id', $productId)->get();
    }
}
