@extends('layouts.app')

@section('content')
    <!-- Content Area -->
    <div class="mt-10">
        <h1 class="text-2xl font-semibold mb-4">Edit Book</h1>

        <!-- Book Form -->
        <form action="{{ route('books.update', ['book' => $book->id]) }}" method="post" enctype="multipart/form-data">
    @csrf
    @method('PUT')

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
            <label for="pdf_book" class="block text-sm font-medium text-gray-600">Upload New PDF Book</label>
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

    <!-- Submit Button -->
    <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded-md">Update Book</button>
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
@endsection
