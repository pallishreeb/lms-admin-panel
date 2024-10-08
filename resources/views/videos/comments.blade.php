@extends('layouts.app')

@section('content')
    <div class="container mx-auto">
        <h1 class="text-2xl font-bold mb-4">All Comments</h1>
        <div class="comments">
            @foreach ($comments as $comment)
                <div class="comment border rounded p-4 mb-4">
                    <div class="flex items-center mb-2">
                        <div class="flex-1">
                            <p><strong>Video Name:</strong> {{ $comment->video->title }}</p>
                            <p><strong>Video ID:</strong> {{ $comment->video_id }}</p>
                            <p><strong>User:</strong> {{ $comment->user->name }}</p>
                            <p><strong>Comment:</strong> {{ $comment->content }}</p>
                            <p><small class="text-gray-500">Posted on: {{ $comment->created_at->diffForHumans() }}</small></p> <!-- Timestamp -->
                        </div>
                        @if ($comment->image)
                            <a href="{{ $comment->image }}" target="_blank">
                                <img src="{{ $comment->image }}" alt="Comment Image" class="ml-4 w-20 rounded-lg">
                            </a>
                        @endif
                    </div>
                    <button class="toggle-replies bg-blue-500 text-white px-4 py-2 rounded">Replies</button>
                    <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this comment?')" class="bg-red-500 text-white px-4 py-2 rounded">Delete</button>
                    </form>
                    <ul class="replies hidden ml-4">
                    @if ($comment->replies->isEmpty())
                        <li class="mt-2 text-gray-500">No replies</li> <!-- No replies message -->
                    @else
                        @foreach ($comment->replies as $reply)
                            <li class="border-b mt-2">
                                <div class="flex items-center">
                                    <div class="flex-1">
                                        <p><strong>User:</strong> {{ $reply->user->name }}</p>
                                        <p>Reply: {{ $reply->content }}</p>
                                        <p><small class="text-gray-500">Replied on: {{ $reply->created_at->diffForHumans() }}</small></p> <!-- Timestamp -->
                                    </div>
                                    <div class="flex flex-col">
                                        @if ($reply->image)
                                            <a href="{{ $reply->image }}" target="_blank">
                                                <img src="{{ $reply->image }}" alt="Reply Image" class="w-20 rounded-lg">
                                            </a>
                                        @endif
                                        <form action="{{ route('admin.replies.destroy', $reply) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Are you sure you want to delete this reply?')" class="text-red-500"><i class="fas fa-trash"></i>Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                        @endif
                    </ul>

                    <!-- Admin Reply Form -->
                    <form action="{{ route('admin.replies.store', $comment->id) }}" method="POST" class="mt-4">
                        @csrf
                        <textarea name="content" rows="2" class="border rounded w-full p-2" placeholder="Reply as admin"></textarea>
                        <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded mt-2">Reply</button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    <style>
        .replies {
            list-style-type: none;
            padding-left: 0;
        }
        .toggle-replies {
            cursor: pointer;
        }
    </style>

    <script>
        document.querySelectorAll('.toggle-replies').forEach(function(button) {
            button.addEventListener('click', function() {
                var repliesList = this.parentElement.querySelector('.replies');
                repliesList.classList.toggle('hidden');
            });
        });
    </script>
@endsection
