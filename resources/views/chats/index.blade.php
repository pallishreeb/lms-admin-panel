@extends('layouts.app')

@section('content')
<div class="bg-white p-4 shadow-md rounded-md">
    <button class="text-l font-semibold mb-4 bg-yellow-500 text-white px-4 py-2 rounded-md">Chat Messages</button>

    @forelse($users as $user)
        <div class="flex items-center justify-between border-b py-3 mx-4">
            <div>
                <span class="mr-4">{{ $user->name }}</span>

            </div>
            <span class="mr-4">{{ $user->email }}</span>
            <a href="{{ route('user-chats', ['user' => $user->id]) }}" class="inline-block bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-400">View Or Reply</a>

            <!-- You can replace the above line with a button for a more interactive UI -->
        </div>
        @empty
                        <div>
                            <h5 colspan="9" class="py-2 px-6 border-b text-center">No Chats found.</h5>
                        </div>
                    @endforelse

</div>
@endsection
