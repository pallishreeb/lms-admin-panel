@extends('layouts.app')

@section('content')
    <!-- Content Area -->
    <div class="mt-10">
        <!-- Buttons -->
        <div class="flex justify-between items-center mb-4 pr-4">
            <!-- Create Book Button -->
            <a href="{{ route('books.create') }}" class="bg-yellow-500 text-white px-4 py-2 rounded-md">Create Book</a>

            <!-- Search Bar -->
            <form action="{{ route('books.index') }}" method="get" class="flex justify-end">
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

        <!-- Book Table -->
        <div class="bg-white rounded-lg shadow-md overflow-x-auto">
            <table class="min-w-full bg-white border">
                <thead>
                    <tr>
                        <!-- <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">ID</th> -->
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Cover Photo</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Pdf-Book</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Pages</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Is Complete</th>
                        {{-- <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Published</th> --}}
                        {{-- <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Is Free</th> --}}
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($books as $book)
                        <tr>
                            <!-- <td class="px-6 py-4 whitespace-no-wrap">{{ $book->id }}</td> -->
                            <td class="px-6 py-4 whitespace-no-wrap">{{ $book->title }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap">
                                @if($book->cover_pic)
                                    <img src="{{$book->cover_pic}}" alt="{{ $book->title }}" class="w-10 h-10 object-cover rounded-full">
                                @else
                                    No Image
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap">{{ $book->category->name }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap">
                                 <a href="{{ $book->pdf_book }}" target="_blank" class="text-blue-500 hover:underline">view</a></td>
                            <td class="px-6 py-4 whitespace-no-wrappy-2 px-4 border-b">{{ $book->price }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap">{{ $book->pages }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap">{{$book->status === "Incomplete" ? 'Incomplete' : 'Completed' }}</td>
                            {{-- <td class="px-6 py-4 whitespace-no-wrap">{{ $book->is_published ? 'Yes' : 'No' }}</td> --}}
                            {{-- <td class="px-6 py-4 whitespace-no-wrap">{{ $book->is_free ? 'Yes' : 'No' }}</td> --}}
                            <td class="px-6 py-4 whitespace-no-wrap">
                                <a href="{{ route('books.edit', ['book' => $book->id]) }}" class="text-yellow-500 hover:underline mr-2"><i class="fas fa-edit"></i>Edit</a>
                                <form action="{{ route('books.destroy', ['book' => $book->id]) }}" method="post" class="inline" onsubmit="return confirmDelete(event)">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline"><i class="fas fa-trash"></i>Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="py-2 px-6 border-b text-center">No courses found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $books->links() }}
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
