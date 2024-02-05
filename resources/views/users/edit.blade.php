@extends('layouts.app')

@section('content')
    <div class="flex justify-center mt-5">
        <div class="bg-white p-8 shadow-md rounded-md w-96">
            <h2 class="text-2xl font-bold mb-4">Edit User</h2>

            <!-- Display current user information -->
            <div class="mb-4">
                <strong>Name:</strong> {{ $user->name }}
            </div>
            <div class="mb-4">
                <strong>Email:</strong> {{ $user->email }}
            </div>
            <form method="post" action="{{ route('users.update', $user->id) }}">
            @csrf
            @method('PUT')
            <!-- Select input for updating the user's role -->
            <div class="mb-4">
                <label for="role" class="block text-sm font-medium text-gray-700">Role:</label>
                <select id="role" name="role" class="mt-1 p-2 w-full border rounded-md">
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ $user->role == $role->name ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Update button -->
            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded-md hover:bg-blue-600">Update</button>
</form>
        </div>
    </div>
@endsection