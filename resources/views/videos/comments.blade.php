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
                            <p><strong>User:</strong> {{ $comment->user->name }}</p> <!-- User's name -->
                            <p><strong>Comment:</strong> {{ $comment->content }}</p>
                        </div>
                        @if ($comment->image)
                            <img src="{{$comment->image }}" alt="Comment Image" class="ml-4 w-20 rounded-lg">
                        @endif
                    </div>
                    <button class="toggle-replies bg-blue-500 text-white px-4 py-2 rounded">Replies</button>
                    <!-- Form to delete comment -->
                    <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this comment?')" class="bg-red-500 text-white px-4 py-2 rounded"><i class="fas fa-trash"></i>Delete</button>
                    </form>
                    <ul class="replies hidden ml-4">
                        @foreach ($comment->replies as $reply)
                            <li class="border-b mt-2">
                                <div class="flex items-center">
                                    <div class="flex-1">
                                        <p><strong>User:</strong> {{ $reply->user->name }}</p> <!-- User's name -->
                                        <p>Reply:{{ $reply->content }}</p>
                                    </div>
                                    <div class="flex flex-col">
                                    @if ($reply->image)
                                        <img src="{{$reply->image }}" alt="Reply Image" class="w-20 rounded-lg">
                                         
                                    @endif
                                   <!-- Form to delete reply -->
                                   <form action="{{ route('admin.replies.destroy', $reply) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Are you sure you want to delete this reply?')" class="text-red-500"><i class="fas fa-trash"></i>Delete</button>
                                    </form>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>
    <style>
        /* CSS styles for comments and replies */
        .replies {
            list-style-type: none;
            padding-left: 0;
        }
        .toggle-replies {
            cursor: pointer;
        }
    </style>
    <script>
        // JavaScript to toggle replies visibility
        document.querySelectorAll('.toggle-replies').forEach(function(button) {
            button.addEventListener('click', function() {
                var repliesList = this.parentElement.querySelector('.replies');
                repliesList.classList.toggle('hidden');
            });
        });

    </script>
@endsection
