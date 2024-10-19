@extends('layouts.app')

@section('content')
<button class="text-l font-semibold mb-4 bg-yellow-500 text-white px-4 py-2 rounded-md">Dashboard</button>
    <!-- Content Area -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-2">

    <!-- Users Box -->
        <div class="bg-white p-2 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold mb-2">Users</h2>
            <p class="text-gray-700 text-2xl">{{$usersCount}}</p>
        </div>

        <!-- Categories Box -->
        <div class="bg-white p-2 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold mb-2">Categories</h2>
            <p class="text-gray-700 text-2xl">{{$categoriesCount}}</p>
        </div>

        <!-- Books Box -->
        <div class="bg-white p-2 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold mb-2">Books</h2>
            <p class="text-gray-700 text-2xl">{{$booksCount}}</p>
        </div>

        <!-- Courses Box -->
        <div class="bg-white p-2 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold mb-2">Courses</h2>
            <p class="text-gray-700 text-2xl">{{$coursesCount}}</p>
        </div>
    </div>
<!-- Logged In Users Section -->
<div class="overflow-x-auto mt-5">
    <table class="min-w-full bg-white border border-gray-300">
        <thead>
            <tr class="bg-gray-100">
                <th class="border-b-2 border-gray-300 p-2 text-left">User ID</th>
                <th class="border-b-2 border-gray-300 p-2 text-left">User Name</th>
                <th class="border-b-2 border-gray-300 p-2 text-left">Device Info</th>
                <th class="border-b-2 border-gray-300 p-2 text-left">Last Activity</th>
                <th class="border-b-2 border-gray-300 p-2 text-left">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($loggedInUsers as $session)
                <tr class="hover:bg-gray-50">
                    <td class="border-b border-gray-300 p-2">{{ $session->user_id }}</td>
                    <td class="border-b border-gray-300 p-2">{{ $session->name }}</td>
                    <td class="border-b border-gray-300 p-2">{{ $session->user_agent }}</td>
                    <td class="border-b border-gray-300 p-2">{{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->toDateTimeString() }}</td>
                    <td class="border-b border-gray-300 p-2">
                        <form action="{{ route('admin.sessions.logout', $session->session_id) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-800">Logout</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>


@endsection
