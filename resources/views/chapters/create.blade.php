@extends('layouts.app')

@section('content')
    <div class="flex justify-center mt-5">
        <div class="w-full max-w bg-white p-8 rounded shadow-md">
            <h1 class="text-2xl font-bold mb-4">Create Chapter</h1>

            @if(session('success'))
                <div class="bg-green-200 p-4 mb-4 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('chapters.store') }}" method="post" enctype="multipart/form-data" id="uploadForm">
                @csrf
                <input type="hidden" name="courseId" value="{{ $course->id }}">

                <div class="grid grid-cols-2 gap-4">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Chapter Title:</label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" class="mt-1 p-2 w-full border rounded-md" required>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Chapter Description:</label>
                        <textarea id="description" name="description" class="mt-1 p-2 w-full border rounded-md" required>{{ old('description') }}</textarea>
                    </div>

                    <!-- Video -->
                    <div>
                        <label for="video" class="block text-sm font-medium text-gray-700">Chapter Video:</label>
                        <input type="file" id="video" name="video" accept="video/mp4" class="mt-1 p-2 w-full border rounded-md" required>
                        
                    </div>

                    <!-- Attachment -->
                    <div>
                        <label for="attachment" class="block text-sm font-medium text-gray-700">Chapter Attachment:</label>
                        <input type="file" id="attachment" name="attachment" accept="application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document" class="mt-1 p-2 w-full border rounded-md">
                    </div>

                    <!-- Position -->
                    <div>
                        <label for="position" class="block text-sm font-medium text-gray-700">Chapter Position:</label>
                        <input type="number" id="position" name="position" value="{{ old('position') }}" class="mt-1 p-2 w-full border rounded-md" required>
                    </div>

                    <!-- Is Published -->
                    <div>
                        <label for="isPublished" class="block text-sm font-medium text-gray-700">Is Published:</label>
                        <select id="isPublished" name="isPublished" class="mt-1 p-2 border rounded-md w-full" required>
                            <option value="1" {{ old('isPublished') == 1 ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ old('isPublished') == 0 ? 'selected' : '' }}>No</option>
                        </select>
                    </div>

                    <!-- Is Free -->
                    <div>
                        <label for="isFree" class="block text-sm font-medium text-gray-700">Is Free:</label>
                        <select id="isFree" name="isFree" class="mt-1 p-2 border rounded-md w-full" required>
                            <option value="1" {{ old('isFree') == 1 ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ old('isFree') == 0 ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="mt-4 bg-yellow-500 text-white p-2 rounded-md hover:bg-blue-600">Create Chapter</button>
            </form>
        </div>
    </div>

@endsection
