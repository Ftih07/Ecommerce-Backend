<?php

namespace App\Repositories\Interfaces;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryRepositoryInterface
{
    /**
     * Get all categories with optional filtering and pagination
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getAll(Request $request): LengthAwarePaginator;

    /**
     * Find a category by ID
     *
     * @param int $id
     * @param bool $withProducts
     * @return Category
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findById(int $id, bool $withProducts = false): Category;

    /**
     * Create a new category
     *
     * @param array $data
     * @return Category
     */
    public function create(array $data): Category;

    /**
     * Update a category
     *
     * @param int $id
     * @param array $data
     * @return Category
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(int $id, array $data): Category;

    /**
     * Delete a category
     *
     * @param int $id
     * @return bool
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function delete(int $id): bool;

    /**
     * Get products in a category
     *
     * @param int $categoryId
     * @return Collection
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getProductsByCategory(int $categoryId): Collection;

    /**
     * Check if a category has products
     *
     * @param int $categoryId
     * @return bool
     */
    public function hasProducts(int $categoryId): bool;
}
