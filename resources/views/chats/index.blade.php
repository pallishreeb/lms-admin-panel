@extends('layouts.app')

@section('content')
<div class="bg-white p-4 shadow-md rounded-md">
    <button class="text-l font-semibold mb-4 bg-yellow-500 text-white px-4 py-2 rounded-md">Chat Messages</button>

    @forelse($users as $user)
        <div class="flex items-center justify-between border-b py-3 mx-4">
            <div class="flex items-center">
                <!-- User profile image or first letter of name -->
                @if($user->profile_image)
                    <img src="{{ $user->profile_image }}" alt="Profile" class="rounded-full w-10 h-10 mr-4">
                @else
                    <div class="rounded-full w-10 h-10 bg-blue-500 flex items-center justify-center text-white mr-4">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif

                <span class="mr-4">{{ $user->name }}</span>
            </div>
            <span class="mr-4">{{ $user->email }}</span>
            <a href="{{ route('user-chats', ['user' => $user->id]) }}" class="inline-block bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-400">View Or Reply</a>
        </div>
    @empty
        <div>
            <h5 colspan="9" class="py-2 px-6 border-b text-center">No Chats found.</h5>
        </div>
    @endforelse

</div>
@endsection
