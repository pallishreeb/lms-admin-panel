<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
            // Add more fields as needed
        ]);

        // Update user details
        auth()->user()->update($request->all());

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully');
    }
}
