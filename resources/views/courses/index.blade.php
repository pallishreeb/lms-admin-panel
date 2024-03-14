@extends('layouts.app')

@section('content')
    <!-- Content Area -->
    <div class="mt-10">
      
        <div class="flex justify-between items-center mb-4 pr-4">
            <!-- Create Book Button -->
            <a href="{{ route('courses.create') }}" class="bg-yellow-500 text-white px-4 py-2 rounded-md">Create Course</a>

             <!-- Search Bar -->
        <form action="{{ route('courses.index') }}" method="get" class="flex justify-end mb-4 pr-4">
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="Search..."
                class="border p-2 rounded-md focus:outline-none focus:border-blue-500"
            />
            <button type="submit" class="ml-2 bg-yellow-500 text-white px-4 py-2 rounded-md">Search</button>
        </form>
        </div>
        <!-- Course Table -->
        <div class="bg-white rounded-lg shadow-md overflow-x-auto">
            <table class="min-w-full bg-white border">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Cover Photo</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Is Published</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Is Free</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                        <tr>
                            <td class="px-6 py-4 whitespace-no-wrap">{{ $course->id }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap">{{ $course->title }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap">
                                @if($course->cover_pic)
                                    <img src="{{$course->cover_pic}}" alt="{{ $course->title }}" class="w-10 h-10 object-cover rounded-full">
                                @else
                                    No Image
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap">${{ $course->price }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap">{{ $course->isPublished ? 'Yes' : 'No' }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap">{{ $course->isFree ? 'Yes' : 'No' }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap">
                                <a href="{{ route('courses.edit', ['course' => $course->id]) }}" class="text-yellow-500 hover:underline mr-2"><i class="fas fa-edit"></i>Edit</a>
                                <form action="{{ route('courses.destroy', ['course' => $course->id]) }}" method="post" class="inline" onsubmit="return confirmDelete(event)">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline"><i class="fas fa-trash"></i>Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-2 px-4 border-b text-center">No courses found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $courses->links() }}
   
    </div>
    <script>
    function confirmDelete(event) {
        event.preventDefault(); // Prevent the default behavior of the form submission

        Swal.fire({
            title: 'Are you sure?',
            text: 'You won\'t be able to revert this!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // If the user confirms, submit the form
                event.target.submit();
            }
        });
    }
</script>
@endsection
