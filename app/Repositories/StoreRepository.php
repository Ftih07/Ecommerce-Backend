<?php

namespace App\Repositories;

use App\Models\Store;
use App\Repositories\Interfaces\StoreRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class StoreRepository implements StoreRepositoryInterface
{
    /**
     * @var Store
     */
    protected $store;

    /**
     * StoreRepository constructor.
     *
     * @param Store $store
     */
    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    /**
     * Get all stores with optional filtering and pagination
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getAll(Request $request): LengthAwarePaginator
    {
        $query = $this->store;

        // Filter by city if specified
        if ($request->has('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        // Filter by name if specified
        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Sort by name asc by default
        $sortField = $request->sort_by ?? 'name';
        $sortOrder = $request->sort_order ?? 'asc';
        $query->orderBy($sortField, $sortOrder);

        // Paginate results
        $perPage = $request->per_page ?? 15;

        return $query->paginate($perPage);
    }

    /**
     * Get all stores without pagination
     *
     * @return Collection
     */
    public function getAllStores(): Collection
    {
        return $this->store->all();
    }

    /**
     * Find store by id
     *
     * @param int $id
     * @return Store|null
     */
    public function findById(int $id): ?Store
    {
        return $this->store->find($id);
    }

    /**
     * Find store by id or fail
     *
     * @param int $id
     * @return Store
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int $id): Store
    {
        return $this->store->findOrFail($id);
    }

    /**
     * Create new store
     *
     * @param array $data
     * @return Store
     */
    public function create(array $data): Store
    {
        return $this->store->create($data);
    }

    /**
     * Update store
     *
     * @param int $id
     * @param array $data
     * @return Store|null
     */
    public function update(int $id, array $data): ?Store
    {
        $store = $this->findById($id);

        if (!$store) {
            return null;
        }

        $store->update($data);
        return $store;
    }

    /**
     * Delete store
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $store = $this->findById($id);

        if (!$store) {
            return false;
        }

        return $store->delete();
    }

    /**
     * Get stores by city
     *
     * @param string $city
     * @param Request|null $request
     * @return LengthAwarePaginator
     */
    public function getByCity(string $city, Request $request = null): LengthAwarePaginator
    {
        $request = $request ?? new Request();
        $query = $this->store->where('city', 'like', '%' . $city . '%');

        // Sort by name asc by default
        $sortField = $request->sort_by ?? 'name';
        $sortOrder = $request->sort_order ?? 'asc';
        $query->orderBy($sortField, $sortOrder);

        // Paginate results
        $perPage = $request->per_page ?? 10;

        return $query->paginate($perPage);
    }

    /**
     * Get stores with their products
     *
     * @param int $id
     * @return Store|null
     */
    public function findByIdWithProducts(int $id): ?Store
    {
        return $this->store->with('products')->find($id);
    }

    /**
     * Search stores by name
     *
     * @param string $name
     * @param Request|null $request
     * @return LengthAwarePaginator
     */
    public function searchByName(string $name, Request $request = null): LengthAwarePaginator
    {
        $request = $request ?? new Request();
        $query = $this->store->where('name', 'like', '%' . $name . '%');

        // Sort by name asc by default
        $sortField = $request->sort_by ?? 'name';
        $sortOrder = $request->sort_order ?? 'asc';
        $query->orderBy($sortField, $sortOrder);

        // Paginate results
        $perPage = $request->per_page ?? 10;

        return $query->paginate($perPage);
    }
}
