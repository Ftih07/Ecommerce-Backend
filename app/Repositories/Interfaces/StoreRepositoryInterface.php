<?php

namespace App\Repositories\Interfaces;

use App\Models\Store;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface StoreRepositoryInterface
{
    /**
     * Get all stores with optional filtering and pagination
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getAll(Request $request): LengthAwarePaginator;

    /**
     * Get all stores without pagination
     *
     * @return Collection
     */
    public function getAllStores(): Collection;

    /**
     * Find store by id
     *
     * @param int $id
     * @return Store|null
     */
    public function findById(int $id): ?Store;

    /**
     * Find store by id or fail
     *
     * @param int $id
     * @return Store
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int $id): Store;

    /**
     * Create new store
     *
     * @param array $data
     * @return Store
     */
    public function create(array $data): Store;

    /**
     * Update store
     *
     * @param int $id
     * @param array $data
     * @return Store|null
     */
    public function update(int $id, array $data): ?Store;

    /**
     * Delete store
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Get stores by city
     *
     * @param string $city
     * @param Request|null $request
     * @return LengthAwarePaginator
     */
    public function getByCity(string $city, Request $request = null): LengthAwarePaginator;

    /**
     * Get stores with their products
     *
     * @param int $id
     * @return Store|null
     */
    public function findByIdWithProducts(int $id): ?Store;

    /**
     * Search stores by name
     *
     * @param string $name
     * @param Request|null $request
     * @return LengthAwarePaginator
     */
    public function searchByName(string $name, Request $request = null): LengthAwarePaginator;
}
