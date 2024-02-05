@extends('layouts.app')

@section('content')
    <!-- Content Area -->
    <div class="mt-10">
        <h1 class="text-2xl font-semibold mb-4">Create Book</h1>

        <!-- Book Form -->
        <form action="{{ route('books.store') }}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="grid grid-cols-2 gap-4">

        <!-- Title -->
        <div class="mb-4">
            <label for="title" class="block text-sm font-medium text-gray-600">Title</label>
            <input type="text" name="title" id="title" class="mt-1 p-2 border rounded-md w-full">
        </div>

        <!-- Category -->
        <div class="mb-4">
            <label for="category_id" class="block text-sm font-medium text-gray-600">Category</label>
            <select name="category_id" id="category_id" class="mt-1 p-2 border rounded-md w-full">
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Price -->
        <div class="mb-4">
            <label for="price" class="block text-sm font-medium text-gray-600">Price</label>
            <input type="number" name="price" id="price" class="mt-1 p-2 border rounded-md w-full">
        </div>

        <!-- Pages -->
        <div class="mb-4">
            <label for="pages" class="block text-sm font-medium text-gray-600">Pages</label>
            <input type="number" name="pages" id="pages" class="mt-1 p-2 border rounded-md w-full">
        </div>

        <!-- Language -->
        <div class="mb-4">
            <label for="language" class="block text-sm font-medium text-gray-600">Language</label>
            <input type="text" name="language" id="language" class="mt-1 p-2 border rounded-md w-full">
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
            <input type="file" name="cover_pic" id="cover_pic" class="mt-1 p-2 border rounded-md w-full">
        </div>

        <!-- Attachment -->
        <div class="mb-4">
            <label for="attachment" class="block text-sm font-medium text-gray-600">Attachment</label>
            <input type="file" name="pdf_book" id="pdf_book" class="mt-1 p-2 border rounded-md w-full">
        </div>

        <!-- Description (full width) -->
        <div class="col-span-2 mb-4">
            <label for="description" class="block text-sm font-medium text-gray-600">Description</label>
            <textarea name="description" id="description" rows="3" class="mt-1 p-2 border rounded-md w-full"></textarea>
        </div>

    </div>

    <!-- Submit Button -->
    <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded-md">Create Book</button>
</form>

    </div>
@endsection
