<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10);
        return UserResource::collection($users);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'nullable|string|in:traveler,provider,content_admin,system_admin',
            'country_code' => 'nullable|string|size:2',
            'photo_url' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:30',
            'budget_level' => 'nullable|string|in:low,medium,high',
            'trip_pace' => 'nullable|string|in:relaxed,balanced,packed',
            'group_type' => 'nullable|string|in:solo,couple,family,friends',
            'activity_level' => 'nullable|string|in:low,moderate,high',
        ]);

        $validatedData['password_hash'] = Hash::make($validatedData['password']);
        unset($validatedData['password']);

        $user = User::create($validatedData);

        return (new UserResource($user))->response()->setStatusCode(201);
        
    }

    public function show(int $id)
    {
        $user = User::findOrFail($id);
        return new UserResource($user);
    }
}
