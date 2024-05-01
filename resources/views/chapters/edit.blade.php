@extends('layouts.app')

@section('content')
    <div class="flex justify-center mt-5">
        <div class="w-full max-w bg-white p-8 rounded shadow-md">
            <h1 class="text-2xl font-bold mb-4">Edit Chapter</h1>
          <!-- 
            @if(session('success'))
                <div class="bg-green-200 p-4 mb-4 rounded">
                    {{ session('success') }}
                </div>
            @endif -->

            <form method="post" action="{{ route('chapters.update', ['chapter' => $chapter]) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="courseId" value="{{ $chapter->course_id }}">
                <div class="grid grid-cols-2 gap-4">
                    <!-- Title -->
                    <div class="col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-700">Chapter Title:</label>
                        <input type="text" id="title" name="title" value="{{ $chapter->title }}" class="mt-1 p-2 w-full border rounded-md" required>
                    </div>
                
                    <div>
                        <div>
                            <!-- Description -->
                        <label for="description" class="block text-sm font-medium text-gray-700">Chapter Description:</label>
                        <textarea id="description" name="description" rows="6" class="mt-1 p-2 w-full border rounded-md" required>{{ $chapter->description }}</textarea>
                        </div>
                         <!-- Existing PDF -->
                    <div class="col">
                        <label for="existing_attachment" class="block text-sm font-medium text-gray-700">Existing PDF:</label>
                        @if($chapter->attachment_url)
                            <a href="{{ $chapter->attachment_url }}" target="_blank" class="text-blue-500 hover:underline">View Existing PDF</a>
                        @else
                            <p>No existing PDF.</p>
                        @endif
                    </div>

                    <!-- Attachment -->
                    <div class="col">
                        <label for="attachment"  class="block text-sm font-medium text-gray-700">Upload New  PDF(Max 500MB):</label>
                        <input type="file" id="attachment" name="attachment" class="mt-1 p-2 w-full border rounded-md" accept="application/pdf">
                        <progress id="progressBarPdf" value="0" max="100" class="w-full" style="display: none;"></progress>
                        <div id="loadingIndicatorPdf" style="display: none;">Uploading...</div>
                        <input type="text" id="attachmentUrl" style="display: none;" name="attachmentUrl">
                    </div>
                     <!-- Is Published -->
                    <!-- <div>
                        <label for="isPublished" class="block text-sm font-medium text-gray-700">Is Published:</label>
                        <select name="isPublished" id="isPublished" class="mt-1 p-2 border rounded-md w-full" required>
                            <option value="1" {{ $chapter->isPublished ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ !$chapter->isPublished ? 'selected' : '' }}>No</option>
                        </select>
                    </div> -->
                    <!-- Is Free -->
                    <!-- <div>
                        <label for="isFree" class="block text-sm font-medium text-gray-700">Is Free:</label>
                        <select name="isFree" id="isFree" class="mt-1 p-2 border rounded-md w-full" required>
                            <option value="1" {{ $chapter->isFree ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ !$chapter->isFree ? 'selected' : '' }}>No</option>
                        </select>
                    </div> -->
                        <!-- Position -->
                        <!-- <div>
                        <label for="position" class="block text-sm font-medium text-gray-700">Chapter Position:</label>
                        <input type="number" id="position" name="position" value="{{ $chapter->position }}" class="mt-1 p-2 w-full border rounded-md" required>
                    </div> -->
                    </div>

                    <!-- Video -->
                    <div>
                        
                        @if($chapter->video_url)
                            <video controls class="mb-2 w-full" style="max-height: 300px;">
                                <source src="{{$chapter->video_url}}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        @endif
                        <label for="video" class="block text-sm font-medium text-gray-700">Upload New Video(Format MP4,Max 500MB):</label>
                        <input type="file" id="video" name="video" class="mt-1 p-2 w-full border rounded-md" accept="video/mp4">
                        <progress id="progressBar" value="0" max="100" class="w-full" style="display: none;"></progress>
                        <div id="loadingIndicator" style="display: none;">Uploading...</div>
                        <input type="text" id="videoUrl" style="display: none;" name="videoUrl">
                    </div>
                </div>

                <div class="flex justify-end mt-1 gap-2"> <!-- Use 'justify-end' class to align items to the right -->
                 <!-- Submit Button -->
                 <button type="submit" class="mt-4 bg-yellow-500 text-white p-2 rounded-md hover:bg-yellow-600">Update Chapter</button>
                <button  class="mt-4 bg-red-500 text-white p-2 rounded-md hover:bg-red-600">
                    <a href="/admin/courses">Cancel</a>
                </button>    
               </div>
               
            </form>
        </div>
    </div>
<script>
    document.getElementById('video').addEventListener('change', function() {
    var progressBar = document.getElementById('progressBar');
    progressBar.style.display = 'block'; // Show the progress bar
    let file = this.files[0];
    let formData = new FormData();
    formData.append('video', file);

    let xhr = new XMLHttpRequest();
    xhr.open('POST', '{{ route("upload.video") }}', true);
    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
        // Show loading indicator when upload starts
        document.getElementById('loadingIndicator').style.display = 'block';
    xhr.upload.onprogress = function(e) {
        if (e.lengthComputable) {
            let percentComplete = (e.loaded / e.total) * 100;
            document.getElementById('progressBar').value = percentComplete;
        }
    };

    xhr.onload = function() {
        // Hide loading indicator when upload completes
        document.getElementById('loadingIndicator').style.display = 'none';

        if (xhr.status === 200) {
            console.log('Upload successful');
            let response = JSON.parse(xhr.responseText);
            let videoUrl = response.videoUrl; // Assuming the response contains the video URL
            document.getElementById('videoUrl').value = videoUrl;
        } else {
            // Error occurred during upload
            console.error('Upload error');
        }
    };

    xhr.onerror = function() {
        // Hide loading indicator on upload error
        document.getElementById('loadingIndicator').style.display = 'none';

        // Handle upload errors
        console.error('Upload error');
    };

    xhr.send(formData);

});

//upload pdf to s3 and return url
document.getElementById('attachment').addEventListener('change', function() {
    var progressBar = document.getElementById('progressBarPdf');
    progressBar.style.display = 'block'; // Show the progress bar
    let file = this.files[0];
    let formData = new FormData();
    formData.append('attachment', file);

    let xhr = new XMLHttpRequest();
    xhr.open('POST', '{{ route("upload.pdf") }}', true);
    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
        // Show loading indicator when upload starts
        document.getElementById('loadingIndicatorPdf').style.display = 'block';
    xhr.upload.onprogress = function(e) {
        if (e.lengthComputable) {
            let percentComplete = (e.loaded / e.total) * 100;
            document.getElementById('progressBarPdf').value = percentComplete;
        }
    };

    xhr.onload = function() {
        // Hide loading indicator when upload completes
        document.getElementById('loadingIndicatorPdf').style.display = 'none';

        if (xhr.status === 200) {
            console.log('Upload successful');
            let response = JSON.parse(xhr.responseText);
            let attachmentUrl = response.attachmentUrl; // Assuming the response contains the video URL
            document.getElementById('attachmentUrl').value = attachmentUrl;
        } else {
            // Error occurred during upload
            console.error('Upload error');
        }
    };

    xhr.onerror = function() {
        // Hide loading indicator on upload error
        document.getElementById('loadingIndicatorPdf').style.display = 'none';

        // Handle upload errors
        console.error('Upload error');
    };

    xhr.send(formData);

});
</script>
@endsection
