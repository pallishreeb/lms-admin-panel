@extends('layouts.app')

@section('content')
    <div class="container mx-auto py-8">
        <h1 class="text-2xl font-bold mb-4">Edit Video</h1>
        <form action="{{ route('videos.update', ['bookId' => $bookId, 'videoId' => $video->id]) }}" method="POST" enctype="multipart/form-data" class="max-w-full mx-auto bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            @csrf
            @method('PUT')
            <div class="flex justify-end mt-1 gap-1"> <!-- Use 'justify-end' class to align items to the right -->
                <button type="submit" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Save</button>
                <button  class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    <a href="{{ route('books.edit', ['book' => $bookId]) }}">Cancel</a>
                </button>
            </div>

            <div class="grid grid-cols-2 gap-4">
   
                    <div class="mb-4">
                        <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Title</label>
                        <input type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="title" name="title" value="{{ $video->title }}" required>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                        <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="description" name="description" rows="3" required>{{ $video->description }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label for="video" class="block text-gray-700 text-sm font-bold mb-2">Video File(Max 500mb)</label>
                        <input type="file" id="video" name="video" accept="video/mp4" class="mt-1 p-2 w-full border rounded-md">
                        <progress id="progressBar" value="0" max="100" class="w-full" style="display: none;"></progress>
                        <div id="loadingIndicator" style="display: none;">Uploading...</div>
                        <input type="text" id="video_url" style="display: none;" name="video_url" value="{{ $video->video_url }}">
                    </div>
              

                    <!-- <div class="mb-4">
                        <label for="attachment" class="block text-gray-700 text-sm font-bold mb-2">Attachment Pdf(Max 200Mb)</label>
                        <input type="file" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="attachment" name="attachment">
                        <progress id="progressBarPdf" value="0" max="100" class="w-full" style="display: none;"></progress>
                        <div id="loadingIndicatorPdf" style="display: none;">Uploading...</div>
                        <input type="text" id="attachment_url" style="display: none;" name="attachment_url" value="{{ $video->attachment_url }}">
                    </div> -->
                <!-- Existing Attachment -->
                <!-- <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Existing Attachment</label>
                    <a href="{{ $video->attachment_url }}" target="_blank" class="text-blue-500 hover:underline">View Existing PDF Attachment</a>
                </div> -->
                    <!-- <div class="mb-4">
                        <label for="position" class="block text-gray-700 text-sm font-bold mb-2">Position</label>
                        <input type="number" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="position" name="position" value="{{ $video->position }}" required>
                    </div> -->

                    <div class="mb-4">
                        <label for="isPublished" class="block text-gray-700 text-sm font-bold mb-2">Is Published</label>
                        <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="isPublished" name="isPublished" required>
                            <option value="1" {{ $video->isPublished == 1 ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ $video->isPublished == 0 ? 'selected' : '' }}>No</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="isFree" class="block text-gray-700 text-sm font-bold mb-2">Is Free</label>
                        <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="isFree" name="isFree" required>
                            <option value="1" {{ $video->isFree == 1 ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ $video->isFree == 0 ? 'selected' : '' }}>No</option>
                        </select>
                    </div>

            </div>
            <!-- Existing Video -->
            <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Existing Video</label>
                    <video controls class="mb-2 w-full" style="max-height: 300px;">
                                <source src="{{$video->video_url}}" type="video/mp4">
                                Your browser does not support the video tag.
                    </video>
                </div>
        </form>
    </div>
    <script>
    document.getElementById('video').addEventListener('change', function() {
    var progressBar = document.getElementById('progressBar');
    progressBar.style.display = 'block'; // Show the progress bar
    let file = this.files[0];
    let formData = new FormData();
    formData.append('video', file);

    let xhr = new XMLHttpRequest();
    xhr.open('POST', '{{ route("book.video") }}', true);
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
            let video_url = response.videoUrl; // Assuming the response contains the video URL
            console.log(video_url)
            document.getElementById('video_url').value = video_url;
        } else {
            // Error occurred during upload
            console.error('Upload error');
        }
    };

    xhr.onerror = function() {
        // Hide loading indicator on upload error
        document.getElementById('loadingIndicator').style.display = 'none';

    // Show an error alert
    alert('An error occurred while uploading the Video. Please try again.');
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
    xhr.open('POST', '{{ route("book.video.pdf") }}', true);
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
            let attachment_url = response.attachmentUrl; // Assuming the response contains the video URL
            console.log(attachment_url)
            document.getElementById('attachment_url').value = attachment_url;
        } else {
            // Error occurred during upload
            console.error('Upload error');
        }
    };

    xhr.onerror = function() {
        // Hide loading indicator on upload error
        document.getElementById('loadingIndicatorPdf').style.display = 'none';

    // Show an error alert
    alert('An error occurred while uploading the PDF. Please try again.');
        // Handle upload errors
        console.error('Upload error');
    };

    xhr.send(formData);

});
</script>
@endsection
