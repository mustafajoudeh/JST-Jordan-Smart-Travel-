<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10);
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email',
            'password_hash' => 'required|string|min:8',
            'role' => 'nullable|string|in:traveler,provider,content_admin,system_admin',
            'country_code' => 'nullable|string|size:2',
            'photo_url' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:30',
            'budget_level' => 'nullable|string|in:low,medium,high',
            'trip_pace' => 'nullable|string|in:relaxed,balanced,packed',
            'group_type' => 'nullable|string|in:solo,couple,family,friends',
            'activity_level' => 'nullable|string|in:low,moderate,high',
        ]);

        $user = User::create($validatedData);

        return response()->json($user, 201);
        
    }

    public function show(int $id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }
}