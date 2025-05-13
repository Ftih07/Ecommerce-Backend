<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryRepository implements CategoryRepositoryInterface
{
    /**
     * @var Category
     */
    protected $category;

    /**
     * CategoryRepository constructor.
     *
     * @param Category $category
     */
    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    /**
     * Get all categories with optional filtering and pagination
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getAll(Request $request): LengthAwarePaginator
    {
        $query = $this->category->query();

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        $perPage = $request->per_page ?? 15;
        return $query->paginate($perPage);
    }

    /**
     * Find a category by ID
     *
     * @param int $id
     * @param bool $withProducts
     * @return Category
     * @throws ModelNotFoundException
     */
    public function findById(int $id, bool $withProducts = false): Category
    {
        $query = $this->category->query();

        if ($withProducts) {
            $query->with('products');
        }

        return $query->findOrFail($id);
    }

    /**
     * Create a new category
     *
     * @param array $data
     * @return Category
     */
    public function create(array $data): Category
    {
        return $this->category->create($data);
    }

    /**
     * Update a category
     *
     * @param int $id
     * @param array $data
     * @return Category
     * @throws ModelNotFoundException
     */
    public function update(int $id, array $data): Category
    {
        $category = $this->findById($id);
        $category->update($data);

        return $category;
    }

    /**
     * Delete a category
     *
     * @param int $id
     * @return bool
     * @throws ModelNotFoundException
     */
    public function delete(int $id): bool
    {
        $category = $this->findById($id);

        if ($this->hasProducts($id)) {
            return false;
        }

        return $category->delete();
    }

    /**
     * Get products in a category
     *
     * @param int $categoryId
     * @return Collection
     * @throws ModelNotFoundException
     */
    public function getProductsByCategory(int $categoryId): Collection
    {
        $category = $this->findById($categoryId);
        return $category->products;
    }

    /**
     * Check if a category has products
     *
     * @param int $categoryId
     * @return bool
     */
    public function hasProducts(int $categoryId): bool
    {
        $category = $this->findById($categoryId);
        return $category->products()->count() > 0;
    }
}
