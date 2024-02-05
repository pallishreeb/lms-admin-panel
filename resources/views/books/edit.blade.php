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
                <img src="{{ asset('book_covers/' . $book->cover_pic) }}" alt="{{ $book->title }}" class="w-20 h-20 object-cover rounded-md mb-2">
            @else
                No Image
            @endif
            <input type="file" name="cover_pic" id="cover_pic" class="mt-1 p-2 border rounded-md w-full">
        </div>

        <!-- Existing PDF Book -->
        <div class="mb-4">
            <label for="pdf_book" class="block text-sm font-medium text-gray-600">Existing PDF Book</label>
            @if($book->pdf_book)
                <a href="{{ asset('pdf_books/' . $book->pdf_book) }}" target="_blank" class="text-blue-500 hover:underline">{{ $book->pdf_book }}</a>
            @else
                No PDF Available
            @endif
            <input type="file" name="pdf_book" id="pdf_book" class="mt-1 p-2 border rounded-md w-full">
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
@endsection
