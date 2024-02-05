@extends('layouts.app')

@section('content')
    <!-- Content Area -->
    <div class="mt-10">
        <h1 class="text-2xl font-semibold mb-4">Edit Category</h1>

        <!-- Category Form -->
        <form action="{{ route('categories.update', ['category' => $category->id]) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-gray-700">Name:</label>
                <input type="text" name="name" id="name" class="border p-2 w-1/2" value="{{ old('name', $category->name) }}" required>
                @error('name')
                    <p class="text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="image" class="block text-gray-700">Image:</label>
                <input type="file" name="image" id="image" class="border p-2 w-1/2" accept="image/*">
                @if($category->image)
                    <img src="{{ asset('category_images/' . $category->image) }}" alt="{{ $category->name }}" class="mt-2 w-32 h-32 object-cover rounded-md">
                @endif
                @error('image')
                    <p class="text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded-md">Update Category</button>
        </form>
    </div>
@endsection
