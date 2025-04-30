<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/users",
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
    public function index()
    {
        $users = User::all();
        return response()->json($users, 200);
    }

    /**
     * @OA\Post(
     *     path="/users",
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
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6', // Minimal panjang password
            'profile_image' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $user = User::create($request->all());
        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/users/{id}",
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
        $user = User::findOrFail($id); // Otomatis return 404 jika tidak ada
        return response()->json($user, 200);
    }

    /**
     * @OA\Put(
     *     path="/users/{id}",
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
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id . ',user_id',
            'password' => 'sometimes|required|string|min:6',
            'profile_image' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $user->update($request->all());
        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ], 200);
    }

    /**
    * @OA\Delete(
    *     path="/users/{id}",
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
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
