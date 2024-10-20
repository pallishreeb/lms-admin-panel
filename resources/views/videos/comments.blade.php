@extends('layouts.app')

@section('content')
    <div class="container mx-auto">
        <h1 class="text-2xl font-bold mb-4">All Comments</h1>
        <div class="comments">
            @foreach ($comments as $comment)
                <div class="comment border rounded p-4 mb-4">
                    <div class="flex items-center mb-2">
                        <!-- User profile image or first letter of name -->
                        @if($comment->user->profile_image)
                            <img src="{{ $comment->user->profile_image }}" alt="Profile" class="rounded-full w-10 h-10">
                        @else
                            <div class="rounded-full w-10 h-10 bg-blue-500 flex items-center justify-center text-white">
                                {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                            </div>
                        @endif
            
                        <div class="ml-4">
                            <p><strong>{{ $comment->user->name }}</strong></p>
                            <p class="text-gray-500 text-sm">Posted on: {{ $comment->created_at->format('F j, Y, g:i A') }}</p>
                            <p>{{ $comment->content }}</p>
                        </div>
                    </div>

                    <!-- Display comment image -->
                    @if ($comment->image)
                        <a href="{{ $comment->image }}" target="_blank">
                            <img src="{{ $comment->image }}" alt="Comment Image" class="ml-4 w-20 rounded-lg">
                        </a>
                    @endif

                    <button class="toggle-replies bg-yellow-500 text-white px-4 py-2 rounded"><i class="fas fa-eye"></i>Replies</button>

                    <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this comment?')" class="bg-red-500 text-white px-4 py-2 rounded"><i class="fas fa-trash"></i> Delete</button>
                    </form>

                    <ul class="replies hidden ml-4">
                        @if ($comment->replies->isEmpty())
                            <li class="mt-2 text-gray-500">No replies</li>
                        @else
                            @foreach ($comment->replies as $reply)
                                <li class="border-b mt-2">
                                    <div class="flex items-center">
                                        @if($reply->user->profile_image)
                                            <img src="{{ $reply->user->profile_image }}" alt="Profile" class="rounded-full w-8 h-8">
                                        @else
                                            <div class="rounded-full w-8 h-8 bg-green-500 flex items-center justify-center text-white">
                                                {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                                            </div>
                                        @endif

                                        <div class="ml-4 flex-1">
                                            <p><strong>{{ $reply->user->name }}</strong></p>
                                            <p class="text-gray-500 text-sm">Replied on: {{ $reply->created_at->format('F j, Y, g:i A') }}</p>
                                            <p>{{ $reply->content }}</p>
                                        </div>

                                        @if ($reply->image)
                                            <a href="{{ $reply->image }}" target="_blank">
                                                <img src="{{ $reply->image }}" alt="Reply Image" class="w-20 rounded-lg mb-2">
                                            </a>
                                        @endif

                                        <!-- Edit reply button -->
                                        <button class="edit-reply text-yellow-500  px-2 py-1 rounded ml-2"
                                            data-reply="{{ $reply->content }}" 
                                            data-image="{{ $reply->image }}" 
                                            data-reply_id="{{ $reply->id }}"><i class="fas fa-edit"></i></button>


                                        <!-- Delete reply button -->
                                        <form action="{{ route('admin.replies.destroy', $reply) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Are you sure you want to delete this reply?')" class="text-red-500">
                                                <i class="fas fa-trash"></i> 
                                            </button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        @endif
                    </ul>

                    <!-- Admin Reply Form -->
                    <form id="reply-form-{{ $comment->id }}" action="{{ route('admin.replies.store', $comment->id) }}" method="POST" enctype="multipart/form-data" class="mt-4">
                        @csrf
                        <textarea name="content" rows="2" class="border rounded w-full p-2" placeholder="Reply as admin"></textarea>
                        <label class="flex items-center mt-2">
                            <input type="file" name="image" accept="image/*" class="hidden image-input">
                            <i class="fas fa-image text-blue-500 text-xl cursor-pointer"></i>
                            <span class="ml-2 image-name">No image selected</span>
                        </label>
                        <img src="" class="w-20 rounded-lg mt-2 image-preview hidden" alt="Selected Image Preview">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded mt-2">Submit Reply</button>
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
        .hidden {
            display: none;
        }
    </style>

    <script>
        document.querySelectorAll('.toggle-replies').forEach(function(button) {
            button.addEventListener('click', function() {
                var repliesList = this.parentElement.querySelector('.replies');
                repliesList.classList.toggle('hidden');
            });
        });

        document.querySelectorAll('.edit-reply').forEach(function(button) {
        button.addEventListener('click', function() {
        var replyContent = this.getAttribute('data-reply');
        var replyImage = this.getAttribute('data-image');
        var replyId = this.getAttribute('data-reply_id'); // Get the reply ID

        // Find the specific reply's form instead of just the first one
        var replyForm = this.closest('.comment').querySelector(`form[id^="reply-form-"]`); // Use an ID selector if your form has a unique ID

        // Check if replyForm is found
        if (replyForm) {
            var textArea = replyForm.querySelector('textarea[name="content"]');
            var imagePreview = replyForm.querySelector('.image-preview');
            var imageName = replyForm.querySelector('.image-name');

            // Fill the form with existing reply content
            textArea.value = replyContent;

            // If an image exists, show the preview
            if (replyImage) {
                imagePreview.src = replyImage;
                imagePreview.classList.remove('hidden');
                imageName.textContent = replyImage.split('/').pop(); // Show image name
            } else {
                imagePreview.classList.add('hidden');
                imageName.textContent = 'No image selected';
            }
            console.log(replyForm); // Check if it's null or undefined

            // Change form action to point to the edit route with the reply ID
            replyForm.action = `/admin/replies/${replyId}/edit`;
        } else {
            console.error('Reply form not found!');
        }
    });
});



        document.querySelectorAll('.image-input').forEach(function(input) {
            input.addEventListener('change', function() {
                var imageName = this.closest('form').querySelector('.image-name');
                var imagePreview = this.closest('form').querySelector('.image-preview');
                
                if (this.files && this.files[0]) {
                    imageName.textContent = this.files[0].name;
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreview.classList.remove('hidden');
                    };
                    reader.readAsDataURL(this.files[0]);
                } else {
                    imagePreview.classList.add('hidden');
                    imageName.textContent = 'No image selected';
                }
            });
        });
    </script>
@endsection
