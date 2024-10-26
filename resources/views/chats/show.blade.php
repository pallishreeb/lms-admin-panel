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
            <form action="{{ route('delete-chat', ['id' => $chat->id]) }}" method="post" onsubmit="return confirmDelete(event)">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-500 hover:text-red-700" title="Delete" >
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
                    <button type="button" id="recordButton" class="text-green-500 text-xl cursor-pointer">
                        <i class="fas fa-microphone"></i> <!-- Audio icon -->
                    </button>
                </label>
            </div>
        
            <!-- Timer display -->
            <div id="timerDisplay" class="mt-2 text-red-500"></div>
            
            <!-- Previews -->
            <div id="previewContainer" class="mt-2"></div>
        
            <button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded mt-4">Reply</button>
        </form>
    </div>
</div>

<!-- JavaScript for handling image and audio previews -->

<script>
    let mediaRecorder;
    let audioChunks = [];
    let recording = false;
    let timerInterval;
    let startTime;
    const recordButton = document.getElementById('recordButton');
    const previewContainer = document.getElementById('previewContainer');
    const timerDisplay = document.getElementById('timerDisplay');

    // Timer function
    function startTimer() {
        startTime = Date.now();
        timerInterval = setInterval(() => {
            const elapsedTime = Date.now() - startTime;
            const seconds = Math.floor(elapsedTime / 1000) % 60;
            const minutes = Math.floor(elapsedTime / (1000 * 60));
            timerDisplay.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
        }, 1000);
    }

    function stopTimer() {
        clearInterval(timerInterval);
        timerDisplay.textContent = '';
    }

    async function startRecording() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaRecorder = new MediaRecorder(stream);

            recording = true;
            audioChunks = [];
            mediaRecorder.start();
            startTimer();

            mediaRecorder.ondataavailable = event => {
                audioChunks.push(event.data);
            };

            mediaRecorder.onstop = () => {
                const audioBlob = new Blob(audioChunks, { type: 'audio/mpeg' });
                const audioUrl = URL.createObjectURL(audioBlob);
                const audio = document.createElement('audio');
                audio.src = audioUrl;
                audio.controls = true;

                // Clear the previous preview and append the new one
                previewContainer.innerHTML = '';
                previewContainer.appendChild(audio);

                // Create a delete button
                const deleteButton = document.createElement('button');
                deleteButton.innerHTML = '<i class="fas fa-trash"></i>';
                deleteButton.className = 'ml-4 mt-2 mb-2 bg-red-500 text-white px-2 py-1 rounded';
                deleteButton.addEventListener('click', () => {
                    previewContainer.innerHTML = '';
                });
                previewContainer.appendChild(deleteButton);

                // Append the Blob data into a form hidden input as a base64 string
                const reader = new FileReader();
                reader.readAsDataURL(audioBlob);
                reader.onloadend = function() {
                    const audioBase64 = reader.result.split(',')[1];

                    // Create a hidden input to store the base64-encoded audio for form submission
                    const audioInput = document.createElement('input');
                    audioInput.type = 'hidden';
                    audioInput.name = 'audio_base64';
                    audioInput.value = audioBase64;
                    previewContainer.appendChild(audioInput);
                };
            };

        } catch (error) {
            console.error('Error accessing microphone:', error);
            if (error.name === 'NotAllowedError') {
                alert('Microphone access is denied. Please check your browser settings to allow microphone access.');
                // Provide specific instructions based on common browsers
                alert('Instructions:\n1. Click on the lock icon in the address bar.\n2. Find the "Microphone" permission.\n3. Change it to "Allow" and refresh the page.');
            } else if (error.name === 'NotFoundError') {
                alert('No microphone found. Please connect a microphone and try again.');
            } else {
                alert('An unexpected error occurred: ' + error.message);
            }
        }
    }

    recordButton.addEventListener('click', () => {
        if (!recording) {
            startRecording();
        } else {
            // Stop recording
            recording = false;
            mediaRecorder.stop();
            stopTimer();
        }
    });

    // Image input event listener
    document.getElementById('imageInput').addEventListener('change', function() {
        const previewContainer = document.getElementById('previewContainer');

        if (this.files && this.files[0]) {
            const imgPreview = document.createElement('img');
            imgPreview.src = URL.createObjectURL(this.files[0]);
            imgPreview.className = 'w-20 rounded-lg mb-2'; // Add margin to separate from audio preview
            previewContainer.appendChild(imgPreview);
        }
    });
</script>


<script>
    function confirmDelete(event) {
        event.preventDefault(); // Prevent the default behavior of the form submission

        Swal.fire({
            title: 'Are you sure?',
            text: 'You won\'t be able to revert this!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // If the user confirms, submit the form
                event.target.submit();
            }
        });
    }
</scrip>

@endsection
