@extends('layouts.app')

@section('content')
<button class="text-l font-semibold mb-4 bg-yellow-500 text-white px-4 py-2 rounded-md">Dashboard</button>
    <!-- Content Area -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-10">

    <!-- Users Box -->
        <div class="bg-white p-4 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold mb-2">Users</h2>
            <p class="text-gray-700 text-2xl">1200</p>
        </div>

        <!-- Categories Box -->
        <div class="bg-white p-4 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold mb-2">Categories</h2>
            <p class="text-gray-700 text-2xl">50</p>
        </div>

        <!-- Books Box -->
        <div class="bg-white p-4 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold mb-2">Books</h2>
            <p class="text-gray-700 text-2xl">800</p>
        </div>

        <!-- Courses Box -->
        <div class="bg-white p-4 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold mb-2">Courses</h2>
            <p class="text-gray-700 text-2xl">150</p>
        </div>
    </div>
@endsection
