@extends('layouts.app')

@section('content')
<div class="bg-white p-4 shadow-md rounded-md">

@if ($errors->any())
    <div class="text-red-500">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

    <h2 class="text-xl font-semibold mb-4">Chats with {{ $user->name }}</h2>

    @foreach($chats as $chat)
        <div class="mb-3 flex items-start justify-between">
            <!-- User profile image and name -->
            <div class="{{ $chat->admin_id ? 'ml-auto' : '' }} flex items-start mt-2">
                @if ($chat->admin_id)
                    @if($user->profile_image)
                        <img src="{{ $user->profile_image }}" alt="User Profile" class="rounded-full w-10 h-10 mr-2">
                    @else
                        <div class="rounded-full w-10 h-10 bg-blue-500 flex items-center justify-center text-white mr-2">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <span class="font-bold text-blue-500">Admin</span>
                @else
                    @if($user->profile_image)
                        <img src="{{ $user->profile_image }}" alt="User Profile" class="rounded-full w-10 h-10 mr-2">
                    @else
                        <div class="rounded-full w-10 h-10 bg-blue-500 flex items-center justify-center text-white mr-2">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <span class="font-bold text-green-500">{{ $user->name }}</span>
                @endif
            </div>

            <!-- Delete button -->
            <form action="{{ route('delete-chat', ['id' => $chat->id]) }}" method="post">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-500 hover:text-red-700" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>

        <!-- Chat message and media -->
        <div class="{{ $chat->admin_id ? 'max-w-xs bg-blue-100 ml-auto' : 'max-w-xs bg-gray-200 ml-12' }} p-2 rounded-md"> <!-- Conditional styling -->
            <p>{{ $chat->message }}</p>

            <!-- Display image if exists -->
            @if ($chat->image)
                <a href="{{ $chat->image }}" target="_blank">
                    <img src="{{ $chat->image }}" alt="Chat Image" class="w-30 rounded-lg mt-2">
                </a>
            @endif

            <!-- Display audio if exists -->
            @if ($chat->audio)
                <audio controls class="mt-2">
                    <source src="{{ $chat->audio }}" type="audio/mpeg">
                    Your browser does not support the audio element.
                </audio>
            @endif

            <span class="text-xs text-gray-500">({{ $chat->created_at->format('Y-m-d H:i:s') }})</span>

        </div>
    @endforeach

    <!-- Reply form with icon-based image and audio upload -->
    <div class="mt-4">
        <form action="{{ route('user-chats.reply', ['user' => $user->id]) }}" method="post" enctype="multipart/form-data" class="mt-4">
            @csrf
            <textarea name="message" class="border p-2 w-full" placeholder="Type your reply..."></textarea>
        
            <div class="flex items-center mt-2">
                <!-- Image Input -->
                <label class="flex items-center">
                    <input type="file" name="image" accept="image/*" id="imageInput" class="hidden">
                    <i class="fas fa-image text-blue-500 text-xl cursor-pointer"></i> <!-- Image icon -->
                </label>
                <!-- Audio Input -->
                <label class="flex items-center ml-4">
                    <input type="file" name="audio" accept="audio/*" id="audioInput" class="hidden">
                    <i class="fas fa-microphone text-green-500 text-xl cursor-pointer"></i> <!-- Audio icon -->
                </label>
            </div>
        
            <!-- Previews -->
            <div id="previewContainer" class="mt-2"></div>
        
            <button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded mt-4">Reply</button>
        </form>
    </div>
</div>

<!-- Previews -->
<script>
    // Function to create and display a preview of the selected image
    function showImagePreview(file) {
        const imgPreview = document.createElement('img');
        imgPreview.src = URL.createObjectURL(file);
        imgPreview.className = 'w-20 rounded-lg mb-2'; // Add margin to separate from audio preview
        return imgPreview;
    }

    // Function to create and display a preview of the selected audio
    function showAudioPreview(file) {
        const audioPreview = document.createElement('audio');
        audioPreview.src = URL.createObjectURL(file);
        audioPreview.controls = true;
        return audioPreview;
    }

    // Image input event listener
    document.getElementById('imageInput').addEventListener('change', function() {
        const previewContainer = document.getElementById('previewContainer');

        if (this.files && this.files[0]) {
            previewContainer.appendChild(showImagePreview(this.files[0]));
        }
    });

    // Audio input event listener
    document.getElementById('audioInput').addEventListener('change', function() {
        const previewContainer = document.getElementById('previewContainer');

        if (this.files && this.files[0]) {
            previewContainer.appendChild(showAudioPreview(this.files[0]));
        }
    });
</script>

@endsection
