@extends('layouts.app')

@section('content')
    <div class="mt-10">
        
        <div class="flex justify-between items-center">
            <h1 class="text-xl font-semibold mb-4">Payment Method Details</h1>

        <a href="{{ route('payment_details.create') }}" class="bg-yellow-500 text-white px-4 py-2 rounded-md">Add Payment Methods</a>
    
        </div>
       
        <table class="mt-4 w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-gray-300 px-4 py-2">ID</th>
                    <th class="border border-gray-300 px-4 py-2">Payment Method</th>
                    <th class="border border-gray-300 px-4 py-2">Payment Number</th>
                    <th class="border border-gray-300 px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($paymentDetails as $paymentDetail)
                    <tr class="hover:bg-gray-100">
                        <td class="border border-gray-300 px-4 py-2">{{ $paymentDetail->id }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $paymentDetail->payment_method }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $paymentDetail->payment_number }}</td>
                        <td class="border border-gray-300 px-4 py-2">
                            <a href="{{ route('payment_details.edit', $paymentDetail->id) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-md">Edit</a>
                            <form action="{{ route('payment_details.destroy', $paymentDetail->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-md">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
