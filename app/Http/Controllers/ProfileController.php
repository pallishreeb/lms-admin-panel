<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    public function show()
    {
        // Retrieve user details
        $user = auth()->user();

        return view('profile.show', compact('user'));
    }

    public function edit()
    {
        // Retrieve user details
        $user = auth()->user();

        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->user()->id,
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Optional profile image validation
            // Add more fields as needed
        ]);
    
        // Retrieve the authenticated user
        $user = auth()->user();
    
        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $profileImage = $request->file('profile_image');
            $imageName = 'user_profile_pics/' . uniqid() . '.' . $profileImage->getClientOriginalExtension(); // Use unique name to avoid collisions
            
            // Store the image in S3
            Storage::disk('s3')->put($imageName, file_get_contents($profileImage));
            
            // Update user's profile image URL
            $user->profile_image = Storage::disk('s3')->url($imageName);
        }
    
        // Update user details
        $user->update($request->only('name', 'email', 'address')); // Add more fields as needed
    
        return redirect()->route('profile.show')->with('success', 'Profile updated successfully');
    }
    
}
