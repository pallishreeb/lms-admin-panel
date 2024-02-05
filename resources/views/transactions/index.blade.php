<!-- resources/views/transactions/index.blade.php -->
@extends('layouts.app')

@section('content')
    <button class="bg-yellow-500 text-white px-4 py-2 rounded-md mb-2">Transaction List</button>

    <table class="min-w-full bg-white border border-gray-300">
        <thead>
            <tr>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">User</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Order</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Product</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
                <tr class="border-b">
                    <td class="px-6 py-4 whitespace-no-wrap">{{ $transaction['user'] }}</td>
                    <td class="px-6 py-4 whitespace-no-wrap">{{ $transaction['order'] }}</td>
                    <td class="px-6 py-4 whitespace-no-wrap">{{ $transaction['product'] }}</td>
                    <td class="px-6 py-4 whitespace-no-wrap">{{ $transaction['amount'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
