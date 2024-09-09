@extends('layouts.app')

@section('content')
    <!-- Content Area -->
    <div class="mt-10">
        <h1 class="text-2xl font-semibold mb-4">Create Book</h1>

        <!-- Book Form -->
        <form action="{{ route('books.store') }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="flex justify-end mt-1 gap-1"> <!-- Use 'justify-end' class to align items to the right -->
       <!-- Submit Button -->
       <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded-md">Create Book</button>
       <!-- cancel Button -->
       <button class="bg-red-500 text-white px-4 py-2 rounded-md"><a href="/admin/books">Cancel</a></button>
    </div>

    <div class="grid grid-cols-2 gap-4">

        <!-- Title -->
        <div class="mb-4">
            <label for="title" class="block text-sm font-medium text-gray-600">Title</label>
            <input type="text" name="title" id="title" class="mt-1 p-2 border rounded-md w-full" required>
        </div>

        <!-- Category -->
        <div class="mb-4">
            <label for="category_id" class="block text-sm font-medium text-gray-600">Category</label>
            <select name="category_id" id="category_id" class="mt-1 p-2 border rounded-md w-full" required>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Price -->
        <div class="mb-4">
            <label for="price" class="block text-sm font-medium text-gray-600">Price</label>
            <input type="number" name="price" id="price" class="mt-1 p-2 border rounded-md w-full" required>
        </div>

        <!-- Pages -->
        <div class="mb-4">
            <label for="pages" class="block text-sm font-medium text-gray-600">Pages</label>
            <input type="number" name="pages" id="pages" class="mt-1 p-2 border rounded-md w-full" required>
        </div>

        <!-- Language -->
        <div class="mb-4">
            <label for="language" class="block text-sm font-medium text-gray-600">Language</label>
            <input type="text" name="language" id="language" class="mt-1 p-2 border rounded-md w-full" required>
        </div>

        <!-- Is Published -->
        <div class="mb-4">
            <label for="is_published" class="block text-sm font-medium text-gray-600">Is Published</label>
            <select name="is_published" id="is_published" class="mt-1 p-2 border rounded-md w-full">
                <option value="1">Yes</option>
                <option value="0">No</option>
            </select>
        </div>
        <!-- Is Free -->
        <div class="mb-4">
            <label for="is_free" class="block text-sm font-medium text-gray-600">Is Free</label>
            <select name="is_free" id="is_free" class="mt-1 p-2 border rounded-md w-full">
                <option value="1">Yes</option>
                <option value="0">No</option>
            </select>
        </div>
        <!-- Cover Picture -->
        <div class="mb-4">
            <label for="cover_pic" class="block text-sm font-medium text-gray-600">Cover Picture</label>
            <input type="file" name="cover_pic" id="cover_pic" class="mt-1 p-2 border rounded-md w-full" required accept="image/*">
        </div>
        <!-- Status -->
        <div class="mb-4">
            <label for="status" class="block text-sm font-medium text-gray-600">Status</label>
            <select name="status" id="status" class="mt-1 p-2 border rounded-md w-full">
                <option value="Incomplete">Incomplete</option>
                <option value="Completed">Completed</option>
            </select>
        </div>
        <!-- Attachment -->
        <div class="mb-4">
            <label for="attachment" class="block text-sm font-medium text-gray-600">Attachment</label>
            <input type="file" name="pdf_book" id="pdf_book" class="mt-1 p-2 border rounded-md w-full" required accept="application/pdf">
            <progress id="progressBarPdf" value="0" max="100" class="w-full" style="display: none;"></progress>
            <div id="loadingIndicatorPdf" style="display: none;">Uploading...</div>
            <input type="text" id="attachmentUrl" style="display: none;" name="attachmentUrl">
        </div>

        <!-- Description (full width) -->
        <div class="col-span-2 mb-4">
            <label for="description" class="block text-sm font-medium text-gray-600">Description</label>
            <textarea name="description" id="description" rows="3"  class="mt-1 p-2 border rounded-md w-full" required></textarea>
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
                console.log("avcd",response)
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
            // Show an error alert
            alert('An error occurred while uploading the PDF. Please try again.');
            // Handle upload errors
            console.error('Upload error');
        };

        xhr.send(formData);

    });
</script>

@endsection
