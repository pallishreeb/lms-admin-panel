@extends('layouts.app')

@section('content')
    <!-- Content Area -->
<div class="mt-10">
      
        <!-- Add New Video Button -->
        <div class="mt-4">
            <a href="{{ route('videos.create', ['bookId' => $book->id]) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-md">Add New Video</a>
        </div>
         <!-- Videos Table -->
        <div class="mt-8">
            <h2 class="text-lg font-semibold mb-4">Videos</h2>
            <table class="w-full border">
                <thead>
                    <tr class="border">
                        <th class="px-4 py-2">Title</th>
                        <th class="px-4 py-2">Video Link</th>
                        <th class="px-4 py-2">Like Count</th>
                        <th class="px-4 py-2">Dislike Count</th>
                        <th class="px-4 py-2">Comments Count</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($book->videos as $video)
                    <tr class="border">
                        <td class="px-4 py-2 text-center">{{ $video->title }}</td>
                        <td class="px-4 py-2 text-center"><button class="copy-btn text-blue-500 hover:underline" data-url="{{ $video->video_url }}">Copy URL</button></td>
                        <td class="px-4 py-2 text-center">{{ $video->getLikesCountAttribute() }}</td>
                        <td class="px-4 py-2 text-center">{{ $video->getDislikesCountAttribute() }}</td>
                        <td class="px-4 py-2 text-center">{{ $video->comments->count() }}
                        <!-- <a href="{{route('admin.comments')}}" class="text-blue-500">view all</a> -->
                        </td>
                        <td class="px-4 py-2 text-center">
                            <a href="{{ route('videos.edit', ['bookId' => $video->book_id, 'videoId' => $video->id]) }}" class="text-blue-500 hover:underline">Edit</a>
                            <form action="{{ route('videos.destroy', ['bookId' => $video->book_id, 'videoId' => $video->id]) }}" method="post" class="inline" onsubmit="return confirmDelete(event)">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline ml-2">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
         <!-- Edit book -->
        <h2 class="text-xl font-semibold mb-4">Edit Book</h2>

        <!-- Book Form -->
        <form action="{{ route('books.update', ['book' => $book->id]) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="flex justify-end mt-1 gap-2"> <!-- Use 'justify-end' class to align items to the right -->
             <!-- Submit Button -->
             <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded-md">Save Updated Book</button>
             <!-- cancel Button -->
            <button class="bg-red-500 text-white px-4 py-2 rounded-md"><a href="/admin/books">Back</a></button>
            </div>


            <div class="grid grid-cols-2 gap-4">

                <!-- Category -->
                <div class="mb-4">
                    <label for="category_id" class="block text-sm font-medium text-gray-600">Category</label>
                    <select name="category_id" id="category_id" class="mt-1 p-2 border rounded-md w-full">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $book->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Title -->
                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium text-gray-600">Title</label>
                    <input type="text" name="title" id="title" value="{{ $book->title }}" class="mt-1 p-2 border rounded-md w-full">
                </div>

                <!-- Price -->
                <div class="mb-4">
                    <label for="price" class="block text-sm font-medium text-gray-600">Price</label>
                    <input type="number" name="price" id="price" value="{{ $book->price }}" class="mt-1 p-2 border rounded-md w-full">
                </div>

                <!-- Pages -->
                <div class="mb-4">
                    <label for="pages" class="block text-sm font-medium text-gray-600">Pages</label>
                    <input type="number" name="pages" id="pages" value="{{ $book->pages }}" class="mt-1 p-2 border rounded-md w-full">
                </div>

                <!-- Language -->
                <div class="mb-4">
                    <label for="language" class="block text-sm font-medium text-gray-600">Language</label>
                    <input type="text" name="language" id="language" value="{{ $book->language }}" class="mt-1 p-2 border rounded-md w-full">
                </div>
                <!-- Status -->
                <div class="mb-4">
                    <label for="status" class="block text-sm font-medium text-gray-600">Status</label>
                    <select name="status" id="status" class="mt-1 p-2 border rounded-md w-full">
                        <option value="Incomplete" {{ $book->status === "Incomplete" ? 'selected' : '' }}>Incomplete</option>
                        <option value="Completed" {{ $book->status === "Completed" ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                <!-- Is Published -->
                <div class="mb-4">
                    <label for="is_published" class="block text-sm font-medium text-gray-600">Is Published</label>
                    <select name="is_published" id="is_published" class="mt-1 p-2 border rounded-md w-full">
                        <option value="1" {{ $book->is_published == 1 ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ $book->is_published == 0 ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                <!-- Is Free -->
                <div class="mb-4">
                    <label for="is_free" class="block text-sm font-medium text-gray-600">Is Free</label>
                    <select name="is_free" id="is_free" class="mt-1 p-2 border rounded-md w-full">
                        <option value="1" {{ $book->is_free == 1 ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ $book->is_free == 0 ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                <!-- Existing Cover Picture -->
                <div class="mb-4">
                    <label for="cover_pic" class="block text-sm font-medium text-gray-600">Existing Cover Picture</label>
                    @if($book->cover_pic)
                        <img src="{{$book->cover_pic}}" alt="{{ $book->title }}" class="w-20 h-20 object-cover rounded-md mb-2">
                    @else
                        No Image
                    @endif
                    <input type="file" name="cover_pic" id="cover_pic" class="mt-1 p-2 border rounded-md w-full">
                </div>

                <!-- Existing PDF Book -->
                <div class="mb-4">
                    <label for="pdf_book" class="block text-sm font-medium text-gray-600">Existing PDF Book</label>
                    @if($book->pdf_book)
                    <div class="flex flex-row gap-2">
                    <a href="{{ $book->pdf_book }}" target="_blank" class="text-blue-500 hover:underline"><i class="fas fa-eye"></i>View existing pdf</a>
                    <a href="{{ route('books.show-pdf', ['id' => $book->id]) }}" class="text-blue-500 hover:underline"><i class="fas fa-edit"></i>Edit existing pdf</a></a>

                    </div>
                    
                    @else
                        No PDF Available
                    @endif
                    <label for="pdf_book" class="block text-sm font-medium text-gray-600">Upload new PDF Book</label>
                    <input type="file" name="pdf_book" id="pdf_book" class="mt-1 p-2 border rounded-md w-full">
                    <progress id="progressBarPdf" value="0" max="100" class="w-full" style="display: none;"></progress>
                    <div id="loadingIndicatorPdf" style="display: none;">Uploading...</div>
                    <input type="text" id="attachmentUrl" style="display: none;" name="attachmentUrl">
                </div>

                <!-- Description (full width) -->
                <div class="col-span-2 mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-600">Description</label>
                    <textarea name="description" id="description" rows="3" class="mt-1 p-2 border rounded-md w-full">{{ $book->description }}</textarea>
                </div>

            </div>
        </form>


</div>
<script>
    //upload pdf to s3 and return url
    document.getElementById('pdf_book').addEventListener('change', function() {
        var progressBar = document.getElementById('progressBarPdf');
        progressBar.style.display = 'block'; // Show the progress bar
        let file = this.files[0];
        let formData = new FormData();
        formData.append('pdf_book', file);

        let xhr = new XMLHttpRequest();
        xhr.open('POST', '{{ route("upload.pdfBook") }}', true);
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
<script>
    document.querySelectorAll('.copy-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            var videoUrl = this.getAttribute('data-url');
            var tempInput = document.createElement('input');
            tempInput.value = videoUrl;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
            alert('Video URL copied to clipboard');
        });
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
</script>
@endsection
