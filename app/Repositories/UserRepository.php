<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    /**
     * @var User
     */
    protected $user;

    /**
     * UserRepository constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get all users with optional filtering and pagination
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getAll(Request $request): LengthAwarePaginator
    {
        $query = $this->user;

        // Filter by name if specified
        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Filter by email if specified
        if ($request->has('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        // Filter by address if specified
        if ($request->has('address')) {
            $query->where('address', 'like', '%' . $request->address . '%');
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
     * Get all users without pagination
     *
     * @return Collection
     */
    public function getAllUsers(): Collection
    {
        return $this->user->all();
    }

    /**
     * Find user by id
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User
    {
        return $this->user->find($id);
    }

    /**
     * Find user by id or fail
     *
     * @param int $id
     * @return User
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int $id): User
    {
        return $this->user->findOrFail($id);
    }

    /**
     * Find user by email
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return $this->user->where('email', $email)->first();
    }

    /**
     * Create new user
     *
     * @param array $data
     * @return User
     */
    public function create(array $data): User
    {
        return $this->user->create($data);
    }

    /**
     * Update user
     *
     * @param int $id
     * @param array $data
     * @return User|null
     */
    public function update(int $id, array $data): ?User
    {
        $user = $this->findById($id);

        if (!$user) {
            return null;
        }

        $user->update($data);
        return $user;
    }

    /**
     * Delete user
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $user = $this->findById($id);

        if (!$user) {
            return false;
        }

        return $user->delete();
    }

    /**
     * Search users by name
     *
     * @param string $name
     * @param Request|null $request
     * @return LengthAwarePaginator
     */
    public function searchByName(string $name, Request $request = null): LengthAwarePaginator
    {
        $request = $request ?? new Request();
        $query = $this->user->where('name', 'like', '%' . $name . '%');

        // Sort by name asc by default
        $sortField = $request->sort_by ?? 'name';
        $sortOrder = $request->sort_order ?? 'asc';
        $query->orderBy($sortField, $sortOrder);

        // Paginate results
        $perPage = $request->per_page ?? 10;

        return $query->paginate($perPage);
    }

    /**
     * Search users by email
     *
     * @param string $email
     * @param Request|null $request
     * @return LengthAwarePaginator
     */
    public function searchByEmail(string $email, Request $request = null): LengthAwarePaginator
    {
        $request = $request ?? new Request();
        $query = $this->user->where('email', 'like', '%' . $email . '%');

        // Sort by email asc by default
        $sortField = $request->sort_by ?? 'email';
        $sortOrder = $request->sort_order ?? 'asc';
        $query->orderBy($sortField, $sortOrder);

        // Paginate results
        $perPage = $request->per_page ?? 10;

        return $query->paginate($perPage);
    }

    /**
     * Find user with reviews
     *
     * @param int $id
     * @return User|null
     */
    public function findWithReviews(int $id): ?User
    {
        return $this->user->with('reviews')->find($id);
    }

    /**
     * Find user with carts
     *
     * @param int $id
     * @return User|null
     */
    public function findWithCarts(int $id): ?User
    {
        return $this->user->with('carts')->find($id);
    }

    /**
     * Check if user exists
     *
     * @param int $id
     * @return bool
     */
    public function exists(int $id): bool
    {
        return $this->user->where('user_id', $id)->exists();
    }
}
