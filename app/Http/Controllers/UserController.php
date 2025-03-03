<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // GET /users
    public function index()
    {
        $users = User::all();
        return response()->json($users, 200);
    }

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

    // GET /users/{id}
    public function show($id)
    {
        $user = User::findOrFail($id); // Otomatis return 404 jika tidak ada
        return response()->json($user, 200);
    }

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

    // DELETE /users/{id}
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
