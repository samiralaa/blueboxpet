<?php

namespace App\Http\Controllers\Api\Auth;
use Illuminate\Support\Facades\Storage;

use App\Events\VerificationCodeGenerated;
use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\Mail\VerificationCodeMail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Validator;

class RegisterController extends Controller
{

    public function create(Request $request)
{
    // Validate the incoming request data
    $validatedData = $request->validate([
        'username' => 'required|string|unique:users|max:255',
        'type' => 'required|in:0,1,2,3,4,6',
        'password' => 'required|string|min:8|confirmed',
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'instagram_link' => 'nullable|url',
        'facebook_link' => 'nullable|url',
        'tiktok_link' => 'nullable|url',
        'phone' => 'nullable|string|max:15',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max size: 2MB
    ]);

    // Handle image upload
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('uploads/users', 'public');
    } else {
        $imagePath = null;
    }

    // Create a new user
    $user = User::create([
        'username' => $validatedData['username'],
        'type' => $validatedData['type'],
        'password' => Hash::make($validatedData['password']),
        'first_name' => $validatedData['first_name'],
        'last_name' => $validatedData['last_name'],
        'instagram_link' => $validatedData['instagram_link'] ?? null,
        'facebook_link' => $validatedData['facebook_link'] ?? null,
        'tiktok_link' => $validatedData['tiktok_link'] ?? null,
        'phone' => $validatedData['phone'] ?? null,
        'read_password' => $validatedData['password'],
        'image' => $imagePath, // Store image path in the database
    ]);

    return response()->json([
        'message' => 'User created successfully',
        'user' => $user
    ], 201);
}

    public function login(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Attempt to log the user in
        if (!Auth::attempt($validatedData)) {
            return response()->json(['message' => 'Unauthorized'], 401); // 401 Unauthorized status
        }

        // Get the currently authenticated user
        $user = Auth::user();

        // Create a new personal access token
        $token = $user->createToken('Personal Access Token')->plainTextToken;

        // Optionally, return a response
        return response()->json([
            'token' => $token,
            'user' => $user,
            'message' => 'User logged in successfully'

        ], 200); // 200 OK status
    }

    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        $user['image']='public/storage/'.$user->image;
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validatedData = $request->validate([
            'username' => 'required|string|unique:users,username,' . $user->id . '|max:255',
            'type' => 'required|in:0,1,2,3,4',
            'password' => 'nullable|string|min:8|confirmed',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'instagram_link' => 'nullable|url',
            'facebook_link' => 'nullable|url',
            'tiktok_link' => 'nullable|url',
            'phone' => 'nullable|string|max:15',
        ]);

        $user->update(array_merge($validatedData, [
            'password' => $request->filled('password') ? Hash::make($validatedData['password']) : $user->password,
        ]));

        return response()->json(['user' => $user]);
    }

    public function delete($id)
    {
        // Attempt to find the user by ID
        $user = User::find($id);

        // Check if user exists
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Delete the user
        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200); // 200 OK status
    }
    
    
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
    public function changePassword(Request $request)
{
    // Validate request
    $request->validate([
        'old_password' => 'required|string',
        'new_password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    $user = Auth::user(); // Get the authenticated user

    // Check if old password matches
    if (!Hash::check($request->old_password, $user->password)) {
        return response()->json(['message' => 'Old password is incorrect'], 400);
    }

    // Update password
    $user->password = Hash::make($request->new_password);
    $user->save();

    return response()->json(['message' => 'Password changed successfully'], 200);
}
}
