@extends('layouts.app')

@section('content')
    <div class="mt-10">
        <h1 class="text-2xl font-semibold mb-4">Create Payment Method</h1>

        <form action="{{ route('payment_details.store') }}" method="post">
            @csrf

            <div class="mb-4">
                <label for="payment_method" class="block text-gray-700">Payment Method:</label>
                <input type="text" name="payment_method" id="payment_method" class="border p-2 w-1/2" value="{{ old('payment_method') }}" required>
                @error('payment_method')
                    <p class="text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="payment_number" class="block text-gray-700">Payment Number:</label>
                <input type="text" name="payment_number" id="payment_number" class="border p-2 w-1/2" value="{{ old('payment_number') }}" required>
                @error('payment_number')
                    <p class="text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">Create</button>
        </form>
    </div>
@endsection
