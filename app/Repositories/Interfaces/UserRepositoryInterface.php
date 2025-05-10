<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    /**
     * Get all users with optional filtering and pagination
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getAll(Request $request): LengthAwarePaginator;

    /**
     * Get all users without pagination
     *
     * @return Collection
     */
    public function getAllUsers(): Collection;

    /**
     * Find user by id
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User;

    /**
     * Find user by id or fail
     *
     * @param int $id
     * @return User
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int $id): User;

    /**
     * Find user by email
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User;

    /**
     * Create new user
     *
     * @param array $data
     * @return User
     */
    public function create(array $data): User;

    /**
     * Update user
     *
     * @param int $id
     * @param array $data
     * @return User|null
     */
    public function update(int $id, array $data): ?User;

    /**
     * Delete user
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Search users by name
     *
     * @param string $name
     * @param Request|null $request
     * @return LengthAwarePaginator
     */
    public function searchByName(string $name, Request $request = null): LengthAwarePaginator;

    /**
     * Search users by email
     *
     * @param string $email
     * @param Request|null $request
     * @return LengthAwarePaginator
     */
    public function searchByEmail(string $email, Request $request = null): LengthAwarePaginator;

    /**
     * Find user with reviews
     *
     * @param int $id
     * @return User|null
     */
    public function findWithReviews(int $id): ?User;

    /**
     * Find user with carts
     *
     * @param int $id
     * @return User|null
     */
    public function findWithCarts(int $id): ?User;

    /**
     * Check if user exists
     *
     * @param int $id
     * @return bool
     */
    public function exists(int $id): bool;
}
