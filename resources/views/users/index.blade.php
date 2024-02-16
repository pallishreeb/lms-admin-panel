@extends('layouts.app')

@section('content')
    <!-- Content Area -->
    <div class="mt-10">
        <!-- Search Bar -->
      
        <div class="flex justify-between items-center mb-4 pr-4">
            <!-- Create Book Button -->
            <a href="#" class="bg-yellow-500 text-white px-4 py-2 rounded-md">All Users</a>

            <!-- Search Bar -->
            <form action="{{ route('admin.users.index') }}" method="get" class="flex justify-end mb-4 pr-4">
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
        <!-- User Table -->
        <div class="bg-white rounded-lg shadow-md overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Address</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-no-wrap">{{ $user->id }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap">{{ $user->name }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap">
                            @if ($user->mobile_number !== null)
                                {{ $user->mobile_number }}
                            @else
                                NA
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap">
                            @if ($user->address !== null)
                                {{ $user->address}}
                            @else
                                NA
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap">
                        <a href="{{ route('users.edit-user', $user->id) }}" class="text-yellow-500 hover:underline">
                        <i class="fas fa-edit"></i>Edit</a>

                        <form action="{{ route('users.destroy', ['user' => $user->id]) }}" method="post" class="inline" onsubmit="return confirmDelete(event)">
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
