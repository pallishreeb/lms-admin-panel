@extends('layouts.app')

@section('content')
    <!-- Content Area -->
    <div class="mt-10">
        <h1 class="text-2xl font-semibold mb-4">Create Category</h1>

        <!-- Category Form -->
        <form action="{{ route('categories.store') }}" method="post" enctype="multipart/form-data">
            @csrf
         

            <div class="mb-4">
                <label for="name" class="block text-gray-700">Name:</label>
                <input type="text" name="name" id="name" class="border p-2 w-1/2" value="{{ old('name') }}" required>
                @error('name')
                    <p class="text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="image" class="block text-gray-700">Image:</label>
                <input type="file" name="image" id="image" class="border p-2 w-1/2" accept="image/*">
                @error('image')
                    <p class="text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="type" class="block text-gray-700">Type:</label>
                <select name="type" id="type" class="border p-2 w-1/2" required>
                    <option value="" disabled selected>Select type</option>
                    <option value="Book">Book</option>
                    <option value="Course">Course</option>
                </select>
                @error('type')
                    <p class="text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="price" class="block text-gray-700">Price:</label>
                <input type="number" step="0.01" name="price" id="price" class="border p-2 w-1/2" value="{{ old('price') }}" required>
                @error('price')
                    <p class="text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex justify-start mt-1 gap-1"> 
            <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded-md">Create Category</button>
            <button class="bg-red-500 text-white px-4 py-2 rounded-md"><a href="/admin/categories">cancel</a></button>
            </div>
        </form>
    </div>
@endsection
