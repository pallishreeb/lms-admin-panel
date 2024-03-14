@extends('layouts.app')

@section('content')
    <div class="flex justify-center mt-5">
        <div class="w-full max-w bg-white p-8 rounded shadow-md">
            <h1 class="text-2xl font-bold mb-2">Edit Course</h1>

            <!-- @if (session('success'))
    <div class="bg-green-200 p-4 mb-4 rounded">
                            {{ session('success') }}
                        </div>
    @endif -->
    <div id="chapters">

<!-- Chapters Section -->
<div class="col-span-2 mb-4">
    <div class="flex items-center justify-between px-3 py-1">
        <h2 class="text-md font-medium mb-2">Chapters</h2>
        <!-- Button to add new chapter -->
        <a href="{{ route('chapters.create', ['courseId' => $course->id]) }}"
            class="text-white px-5 py-2 rounded-md bg-yellow-500 hover:bg-blue-600">Add Chapter</a>
    </div>


    <!-- List existing chapters -->
    @if (is_array($course->chapters) && count($course->chapters) > 0)
      
        <ul>
            @foreach ($course->chapters as $chapterId)
                @php
                    $chapter = \App\Models\Chapter::find($chapterId);
                @endphp
                @if ($chapter)
                    <li
                        class="flex items-center  justify-between py-3 pl-5 pr-8 bg-white rounded border">
                        <span class="font-medium text-md"> {{ $chapter->title }}</span>
                        <div class=" flex gap-5">
                            <a href="{{ route('chapters.edit', ['chapter' => $chapter]) }}"
                                class="text-blue-500 hover:underline">Edit</a>
                            <form action="{{ route('chapters.destroy', ['chapter' => $chapter]) }}"
                                method="post" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-red-500 hover:underline">Delete</button>
                            </form>
                        </div>

                    </li>
                @endif
            @endforeach
        </ul>
    @else
        <p>No chapters available for this course.</p>
    @endif




</div>
</div>
            <form method="post" action="{{ route('courses.update', ['course' => $course]) }}" enctype="multipart/form-data"
                class=" grid grid-cols-2 gap-4">
                @csrf
                @method('PUT')

                <!-- Category -->


                <div class='col-span-1'>
                    <label for="title" class="block text-sm font-medium text-gray-700">Course Title:</label>
                    <input type="text" id="title" name="title" value="{{ $course->title }}"
                        class="mt-1 p-2 w-full border rounded-md">
                </div>
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700">Course Price:</label>
                    <input type="number" id="price" name="price" value="{{ $course->price }}"
                        class="mt-1 p-2 w-full border rounded-md">
                </div>
                <div>
                    <label for="isPublished" class="block text-sm font-medium text-gray-600">Is Published:</label>
                    <select name="isPublished" id="isPublished" class="mt-1 p-2 border rounded-md w-full">
                        <option value="1" {{ $course->isPublished ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ !$course->isPublished ? 'selected' : '' }}>No</option>
                    </select>
                </div>

                <div>
                    <div>
                        <label for="isFree" class="block text-sm font-medium text-gray-600">Is Free:</label>
                        <select name="isFree" id="isFree" class="mt-1 p-2 border rounded-md w-full">
                            <option value="1" {{ $course->isFree ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ !$course->isFree ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                </div>
               
                         <!-- Category -->
                    <div class="mb-4">
                        <label for="category_id" class="block text-sm font-medium text-gray-600">Category</label>
                        <select name="category_id" id="category_id" class="mt-1 p-2 border rounded-md w-full">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $course->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                     <!-- Cover pic -->
                    <div class="mb-4">
                            <label for="cover_pic" class="block text-sm font-medium text-gray-700">Update Cover
                                Picture:</label>
                            <input type="file" name="cover_pic" id="cover_pic" class="mt-1 p-2 w-full border rounded-md">
                    </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Course Description:</label>
                    <textarea id="description" name="description" rows="5" class="mt-1 p-2 w-full border rounded-md">{{ $course->description }}</textarea>
                </div>

                <!-- Existing Cover Picture -->
                <div>
                    <label for="existing_cover_pic" class="block text-sm font-medium text-gray-700">Existing Cover
                        Picture:</label>
                    @if ($course->cover_pic)
                        <img src="{{$course->cover_pic}}" alt="Current Cover Picture"
                            class="mb-2 w-4/6 h-36 ">
                        <p>Current Cover Picture: {{ $course->cover_pic }}</p>
                    @else
                        <p>No existing cover picture.</p>
                    @endif
                </div>
                 <div></div>
               
                <!-- Add other fields as needed -->

                <button type="submit"
                    class="w-full bg-yellow-500 col-span-2 text-white p-2 rounded-md hover:bg-blue-600">Update
                    Course</button>
            </form>
        </div>
    </div>
@endsection
