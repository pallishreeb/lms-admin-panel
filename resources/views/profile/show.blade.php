<!-- resources/views/profile/show.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="container mx-auto mt-10 p-8 bg-white shadow-md rounded-md">
        <h1 class="text-3xl font-semibold mb-4">Your Profile</h1>
        <p class="text-lg">Name: {{ $user->name }}</p>
        <p class="text-lg">Email: {{ $user->email }}</p>
        @if($user->address)
            <p class="text-lg">Address: {{ $user->address }}</p>
        @else
            <p class="text-lg text-gray-500">Address: Not updated</p>
        @endif
        <!-- Add more fields as needed -->

        <div class="mt-6">
            <a href="{{ route('profile.edit') }}" class="inline-block bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600 focus:outline-none focus:shadow-outline-blue active:bg-yellow-800">
                Edit Profile
            </a>
        </div>
    </div>
@endsection

