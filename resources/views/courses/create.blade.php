@extends('layouts.app')

@section('content')
    <div class="flex justify-center mt-5">
        <div class="w-full  bg-white p-8 rounded shadow-md">
            <h1 class="text-2xl font-bold mb-4">Create Course</h1>
            <form method="post" action="{{ route('courses.store') }}" enctype="multipart/form-data" class="grid grid-cols-2 gap-4">
    @csrf

    <!-- Course Title -->
    <div class="mb-4 col-span-2 ">
        <label for="title" class="block text-sm font-medium text-gray-700">Course Title:</label>
        <input type="text" id="title" name="title" class="mt-1 p-2 w-full border rounded-md">
    </div>

    <!-- Course Description -->
    <div class="mb-4">
        <label for="description" class="block text-sm font-medium text-gray-700">Course Description:</label>
        <textarea id="description" name="description" rows="5"  class="mt-1 p-2 w-full border rounded-md"></textarea>
    </div>

    <!-- Course Price -->
    <div class="mb-4 flex flex-col gap-4 py-2">
        <div>
        <label for="price" class="block text-sm font-medium text-gray-700">Course Price:</label>
        <input type="number" id="price" name="price" class="mt-1 p-2 w-full border rounded-md">

        </div>
     
        <div>
        <label for="category_id" class="block text-sm font-medium text-gray-600">Category</label>
        <select name="category_id" id="category_id" class="mt-1 p-2 border rounded-md w-full">
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
        </div>
     
    </div>

    <!-- Cover Picture -->
    <div class="mb-4">
        <label for="cover_pic" class="block text-sm font-medium text-gray-700">Cover Picture:</label>
        <input type="file" name="cover_pic" id="cover_pic" class="mt-1 p-2 w-full border rounded-md">
    </div>

    <!-- Is Published -->
    <div class="mb-4">
        <label for="isPublished" class="block text-sm font-medium text-gray-700">Is Published:</label>
        <select name="isPublished" id="isPublished" class="mt-1 p-2 border rounded-md w-full">
            <option value="1">Yes</option>
            <option value="0">No</option>
        </select>
    </div>
    <div class="mb-4">
        <label for="isFree" class="block text-sm font-medium text-gray-700">Is Free:</label>
        <select name="isFree" id="isFree" class="mt-1 p-2 border rounded-md w-full">
            <option value="1">Yes</option>
            <option value="0">No</option>
        </select>
    </div>
    <div class="flex justify-end mt-1 gap-1 col-span-2"> <!-- Use 'justify-end' class to align items to the right -->
                 <!-- Submit Button -->
                 <button type="submit" class="mt-4 bg-yellow-500 text-white p-2 rounded-md hover:bg-yellow-600">Create Course</button>
                <button  class="mt-4 bg-red-500 text-white p-2 rounded-md hover:bg-red-600">
                    <a href="{{ route('courses.index') }}">Cancel</a>
                </button>    
    </div>
   
</form>


        </div>
    </div>
@endsection
