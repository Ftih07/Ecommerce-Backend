<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * @var UserRepositoryInterface
     */
    protected $userRepository;

    /**
     * UserController constructor.
     *
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @OA\Get(
     *     path="/users",
     *     security={{"bearerAuth":{}}},
     *     summary="Get all users",
     *     description="Mengambil semua data user",
     *     operationId="getUsers",
     *     tags={"Users"},
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mengambil data user",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User"))
     *     )
     * )
     */
    // GET /users
    public function index(Request $request)
    {
        $users = $request->has(['per_page', 'page', 'sort_by', 'sort_order', 'name', 'email', 'address', 'from_date', 'to_date'])
            ? $this->userRepository->getAll($request)
            : $this->userRepository->getAllUsers();

        return response()->json($users, 200);
    }

    /**
     * @OA\Post(
     *     path="/users",
     *     security={{"bearerAuth":{}}},
     *     summary="Create a new user",
     *     description="Membuat user baru",
     *     operationId="createUser",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="Budi"),
     *             @OA\Property(property="email", type="string", format="email", example="budi@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123"),
     *             @OA\Property(property="profile_image", type="string", nullable=true, example="http://example.com/image.jpg"),
     *             @OA\Property(property="address", type="string", nullable=true, example="Jl. Mawar No. 5")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User berhasil dibuat",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User created successfully"),
     *             @OA\Property(property="user", ref="#/components/schemas/User")
     *         )
     *     )
     * )
     */
    // POST /users
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6', // Minimal panjang password
            'profile_image' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $this->userRepository->create($request->all());
            return response()->json([
                'message' => 'User created successfully',
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/users/{id}",
     *     security={{"bearerAuth":{}}},
     *     summary="Get a user by ID",
     *     description="Mengambil data user berdasarkan ID",
     *     operationId="getUserById",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID user",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User ditemukan",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User tidak ditemukan"
     *     )
     * )
     */
    // GET /users/{id}
    public function show($id)
    {
        try {
            $user = $this->userRepository->findOrFail($id);
            return response()->json($user, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'User not found'], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/users/{id}",
     *     security={{"bearerAuth":{}}},
     *     summary="Update user data",
     *     description="Memperbarui data user berdasarkan ID",
     *     operationId="updateUser",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID user",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Andi"),
     *             @OA\Property(property="email", type="string", format="email", example="andi@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="newpassword123"),
     *             @OA\Property(property="profile_image", type="string", nullable=true, example="http://example.com/new.jpg"),
     *             @OA\Property(property="address", type="string", nullable=true, example="Jl. Kenanga No. 7")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User berhasil diperbarui",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User updated successfully"),
     *             @OA\Property(property="user", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User tidak ditemukan"
     *     )
     * )
     */

    // PUT/PATCH /users/{id}
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|unique:users,email,' . $id . ',user_id',
                'password' => 'sometimes|required|string|min:6',
                'profile_image' => 'nullable|string',
                'address' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $this->userRepository->update($id, $request->all());

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            return response()->json([
                'message' => 'User updated successfully',
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
    * @OA\Delete(
    *     path="/users/{id}",
    *     security={{"bearerAuth":{}}},
    *     summary="Delete a user",
    *     description="Menghapus user berdasarkan ID",
    *     operationId="deleteUser",
    *     tags={"Users"},
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         required=true,
    *         description="ID user",
    *         @OA\Schema(type="integer", example=1)
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="User berhasil dihapus",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="User deleted successfully")
    *         )
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="User tidak ditemukan"
    *     )
    * )
    */

    // DELETE /users/{id}
    public function destroy($id)
    {
        try {
            $result = $this->userRepository->delete($id);

            if (!$result) {
                return response()->json(['message' => 'User not found'], 404);
            }

            return response()->json(['message' => 'User deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/users/search/name/{name}",
     *     security={{"bearerAuth":{}}},
     *     summary="Search users by name",
     *     description="Search for users whose name contains the given string",
     *     operationId="searchUsersByName",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="name",
     *         in="path",
     *         required=true,
     *         description="Name to search for",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Number of results per page",
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Search results",
     *         @OA\JsonContent(type="object")
     *     )
     * )
     */
    public function searchByName($name, Request $request)
    {
        try {
            $users = $this->userRepository->searchByName($name, $request);
            return response()->json($users, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error searching users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/users/search/email/{email}",
     *     security={{"bearerAuth":{}}},
     *     summary="Search users by email",
     *     description="Search for users whose email contains the given string",
     *     operationId="searchUsersByEmail",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="email",
     *         in="path",
     *         required=true,
     *         description="Email to search for",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Number of results per page",
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Search results",
     *         @OA\JsonContent(type="object")
     *     )
     * )
     */
    public function searchByEmail($email, Request $request)
    {
        try {
            $users = $this->userRepository->searchByEmail($email, $request);
            return response()->json($users, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error searching users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/users/{id}/with-reviews",
     *     security={{"bearerAuth":{}}},
     *     summary="Get a user with their reviews",
     *     description="Retrieves user data along with all reviews they've written",
     *     operationId="getUserWithReviews",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User with reviews",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function getUserWithReviews($id)
    {
        try {
            $user = $this->userRepository->findWithReviews($id);

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            return response()->json($user, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving user with reviews',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/users/{id}/with-carts",
     *     security={{"bearerAuth":{}}},
     *     summary="Get a user with their shopping carts",
     *     description="Retrieves user data along with all their shopping carts",
     *     operationId="getUserWithCarts",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User with carts",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function getUserWithCarts($id)
    {
        try {
            $user = $this->userRepository->findWithCarts($id);

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            return response()->json($user, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving user with carts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user roles
     *
     * @OA\Put(
     *     path="/users/{id}/roles",
     *     security={{"bearerAuth":{}}},
     *     summary="Update user roles",
     *     description="Updates the roles of a specific user. Requires admin privileges.",
     *     operationId="updateUserRoles",
     *     tags={"Users"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"roles"},
     *             @OA\Property(property="roles", type="array", @OA\Items(type="string"), example={"admin", "seller"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User roles updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User roles updated successfully"),
     *             @OA\Property(property="user", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateRoles(Request $request, $id)
    {
        try {
            // Find user
            $user = $this->userRepository->findById($id);
            
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            // Validate the request
            $validator = Validator::make($request->all(), [
                'roles' => 'required|array',
                'roles.*' => 'exists:roles,name'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get role IDs from role names
            $roleIds = \App\Models\Role::whereIn('name', $request->roles)->pluck('role_id')->toArray();
            
            // Sync user roles (this will remove any existing roles and add the new ones)
            $user->roles()->sync($roleIds);

            // Return updated user with roles
            $user->load('roles');
            
            return response()->json([
                'message' => 'User roles updated successfully',
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating user roles',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
