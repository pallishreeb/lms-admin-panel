<!-- resources/views/profile/edit.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="container mx-auto mt-10 p-8 bg-white shadow-md rounded-md">
        <h1 class="text-3xl font-semibold mb-4">Edit Your Profile</h1>
        <form action="{{ route('profile.update') }}" method="post">
            @csrf
            @method('put')

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-600">Name:</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="mt-1 p-2 border rounded-md w-1/2">
                @error('name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-600">Email:</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="mt-1 p-2 border rounded-md w-1/2">
                @error('email')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="address" class="block text-sm font-medium text-gray-600">Address:</label>
                <input type="text" name="address" value="{{ old('address', $user->address) }}" class="mt-1 p-2 border rounded-md w-1/2">
                <!-- Add more fields as needed -->
            </div>

            <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600 focus:outline-none focus:shadow-outline-blue active:bg-yellow-800">
                Update Profile
            </button>
        </form>
    </div>
@endsection
