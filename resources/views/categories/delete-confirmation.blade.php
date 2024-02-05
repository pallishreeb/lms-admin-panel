@extends('layouts.app')

@section('content')
    <div class="container mx-auto mt-8">
        <h2 class="text-2xl font-bold mb-4">Confirm Deletion</h2>

        <p>Are you sure you want to delete the {{ $type }} '{{ $item->name }}'?</p>

        <form method="post" action="{{ $route }}" class="mt-4">
            @csrf
            @method('DELETE')

            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">Delete</button>
            <a href="{{ $backRoute }}" class="ml-2 text-gray-600">Cancel</a>
        </form>
    </div>
@endsection