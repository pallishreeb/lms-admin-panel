@extends('layouts.app')

@section('content')
<div class="bg-white p-4 shadow-md rounded-md">
    <h2 class="text-xl font-semibold mb-4">Chats with {{ $user->name }}</h2>
    @foreach($chats as $chat)
    <div class="mb-3 flex items-start justify-{{ $chat->admin_id ? 'end' : 'start' }}">
        <div class="max-w-xs bg-gray-200 p-2 rounded-md">
            @if ($chat->admin_id)
                <span class="font-bold text-blue-500">Admin:</span>
            @else
                <span class="font-bold text-green-500">{{ $user->name }}:</span>
            @endif

            <span>{{ $chat->message }}</span>
            @if ($chat->image)
           <img src="{{$chat->image }}" alt="Chat Image" class="w-30 rounded-lg">
                                         
           @endif
            <span class="text-xs text-gray-500">({{ $chat->created_at->diffForHumans() }})</span>
        </div>

        <!-- Add delete button here -->
        <form action="{{ route('delete-chat', ['id' => $chat->id]) }}" method="post">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-red-500 hover:text-red-700 ml-2 mt-2">
                Delete
            </button>
        </form>
    </div>
@endforeach



    <div class="mt-4">
        <form action="{{ route('user-chats.reply', ['user' => $user->id]) }}" method="post" class="mt-4">
        @csrf
        <textarea name="message" class="border p-2 w-full" placeholder="Type your reply..."></textarea>
        <button type="submit" class="bg-yellow-600  text-white px-4 py-2 rounded mt-2">Reply</button>
    </form>
    </div>
</div>
@endsection
