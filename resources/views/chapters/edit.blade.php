@extends('layouts.app')

@section('content')
    <div class="flex justify-center mt-5">
        <div class="w-full max-w bg-white p-8 rounded shadow-md">
            <h1 class="text-2xl font-bold mb-4">Edit Chapter</h1>
<!-- 
            @if(session('success'))
                <div class="bg-green-200 p-4 mb-4 rounded">
                    {{ session('success') }}
                </div>
            @endif -->

            <form method="post" action="{{ route('chapters.update', ['chapter' => $chapter]) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="courseId" value="{{ $chapter->course_id }}">
                <div class="grid grid-cols-2 gap-4">
                    <!-- Title -->
                    <div class="col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-700">Chapter Title:</label>
                        <input type="text" id="title" name="title" value="{{ $chapter->title }}" class="mt-1 p-2 w-full border rounded-md" required>
                    </div>

                    <!-- Description -->
                    <div class="col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">Chapter Description:</label>
                        <textarea id="description" name="description" rows="5" class="mt-1 p-2 w-full border rounded-md" required>{{ $chapter->description }}</textarea>
                    </div>

                    <!-- Video -->
                    <div class="col-span-2">
                        <label for="video" class="block text-sm font-medium text-gray-700">Chapter Video:</label>
                        @if($chapter->video_url)
                            <video controls class="mb-2 w-full" style="max-height: 300px;">
                                <source src="{{$chapter->video_url}}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        @endif
                        <input type="file" name="video" class="mt-1 p-2 w-full border rounded-md" accept="video/mp4">
                    </div>

                    <!-- Existing PDF -->
                    <div class="col-span-2">
                        <label for="existing_attachment" class="block text-sm font-medium text-gray-700">Existing PDF:</label>
                        @if($chapter->attachment_url)
                            <a href="{{ $chapter->attachment_url }}" target="_blank" class="text-blue-500 hover:underline">View Existing PDF</a>
                        @else
                            <p>No existing PDF.</p>
                        @endif
                    </div>

                    <!-- Attachment -->
                    <div class="col-span-2">
                        <label for="attachment" class="block text-sm font-medium text-gray-700">Chapter PDF:</label>
                        <input type="file" name="attachment" class="mt-1 p-2 w-full border rounded-md" accept="application/pdf">
                    </div>

                    <!-- Position -->
                    <div>
                        <label for="position" class="block text-sm font-medium text-gray-700">Chapter Position:</label>
                        <input type="number" id="position" name="position" value="{{ $chapter->position }}" class="mt-1 p-2 w-full border rounded-md" required>
                    </div>

                    <!-- Is Published -->
                    <div>
                        <label for="isPublished" class="block text-sm font-medium text-gray-700">Is Published:</label>
                        <select name="isPublished" id="isPublished" class="mt-1 p-2 border rounded-md w-full" required>
                            <option value="1" {{ $chapter->isPublished ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ !$chapter->isPublished ? 'selected' : '' }}>No</option>
                        </select>
                    </div>

                    <!-- Is Free -->
                    <div>
                        <label for="isFree" class="block text-sm font-medium text-gray-700">Is Free:</label>
                        <select name="isFree" id="isFree" class="mt-1 p-2 border rounded-md w-full" required>
                            <option value="1" {{ $chapter->isFree ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ !$chapter->isFree ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="mt-4 bg-yellow-500 text-white p-2 rounded-md hover:bg-blue-600">Update Chapter</button>
            </form>
        </div>
    </div>
@endsection
