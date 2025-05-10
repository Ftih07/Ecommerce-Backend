<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    /**
     * @var \App\Models\Product
     */
    protected $model;

    /**
     * ProductRepository constructor.
     *
     * @param \App\Models\Product $product
     */
    public function __construct(Product $product)
    {
        $this->model = $product;
    }

    /**
     * Get all products with their relationships
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWithRelations()
    {
        return $this->model->with(['store', 'category'])->get();
    }

    /**
     * Get all products
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Find product by id with relationships
     *
     * @param int $id
     * @return \App\Models\Product|null
     */
    public function findByIdWithRelations($id)
    {
        return $this->model->with(['store', 'category', 'reviews', 'productImages'])->find($id);
    }

    /**
     * Find product by id
     *
     * @param int $id
     * @return \App\Models\Product|null
     */
    public function findById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Create new product
     *
     * @param array $data
     * @return \App\Models\Product
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update product
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\Product|null
     */
    public function update($id, array $data)
    {
        $product = $this->findById($id);

        if (!$product) {
            return null;
        }

        $product->update($data);
        return $product;
    }

    /**
     * Delete product
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $product = $this->findById($id);

        if (!$product) {
            return false;
        }

        return $product->delete();
    }

    /**
     * Get all products for a specific category
     *
     * @param int $categoryId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByCategoryId($categoryId)
    {
        return $this->model->where('category_id', $categoryId)->get();
    }

    /**
     * Get all products for a specific store
     *
     * @param int $storeId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByStoreId($storeId)
    {
        return $this->model->where('store_id', $storeId)->get();
    }
}
