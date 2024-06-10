<!-- resources/views/analog_payments/index.blade.php -->
@extends('layouts.app')

@section('content')
{{-- <button class="bg-yellow-500 text-white px-4 py-2 rounded-md mb-2">Digital Payments List</button>
    <table class="min-w-full bg-white border border-gray-300">
        <thead>
            <tr>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">User</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Transcation Date</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Category</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Contact Number</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
                <tr class="border-b">
                    <td class="px-6 py-4 whitespace-no-wrap">{{ $transaction['user'] }}</td>
                    <td class="px-6 py-4 whitespace-no-wrap">{{ $transaction['order'] }}</td>
                    <td class="px-6 py-4 whitespace-no-wrap">10-06-2024</td>
                    <td class="px-6 py-4 whitespace-no-wrap">{{ $transaction['amount'] }}</td>
                    <td class="px-6 py-4 whitespace-no-wrap">{{ $transaction['product'] }}</td>
                    <td class="px-6 py-4 whitespace-no-wrap">9876772347</td>
                </tr>
            @endforeach
        </tbody>
    </table> --}}

    <button class="bg-yellow-500 text-white px-4 py-2 rounded-md mb-2 mt-2">Analog Payments List</button>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($payments as $payment)
            <div class="border rounded-lg overflow-hidden shadow-md">
                <img src="{{$payment->payment_screenshot}}" alt="Payment Screenshot" class="w-full h-40  object-cover object-center">
                <div class="p-2">
                    <p><strong>Payment Number:</strong>{{ $payment->payment_number }}</p>
                    <p><strong>Division:</strong> {{ $payment->division }}, <strong>District:</strong> {{ $payment->district }}</p>
                    <p><strong>Upazilla:</strong> {{ $payment->upazilla }}</p>
                    <p><strong>School Name:</strong> {{ $payment->school_name }}</p>
                    <p><strong>Class:</strong> {{ $payment->class }}</p>
                    <p><strong>Student Name:</strong> {{ $payment->student_name }}, Mobile Number:</strong> {{ $payment->mobile_number }}</p>
                    <p><strong>Status:</strong> {{ $payment->status }}, <strong>Amount:</strong> {{ $payment->amount }}</p>
                    <div class="flex justify-between">
                    <form action="{{ route('admin.payments.updateStatus', $payment->id) }}" method="post" class="mt-1">

                        @csrf
                        @method('put')
                        {{-- <label for="status" class="block mb-1">Update Status:</label> --}}
                        <select name="status" id="status" class="border border-gray-300 rounded-md p-1">
                            <option value="">Select Status</option>
                            <option value="approved">Approve</option>
                            <option value="rejected">Reject</option>
                        </select>
                        <button type="submit" class="bg-green-600 text-white px-4 py-1 mt-1 rounded-md">Update Status</button>
                    </form>
                      <!-- Delete Button -->
                    <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" class="mt-1"  onsubmit="return confirmDelete(event)">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 text-white px-4 py-1 mt-1 rounded-md">Delete</button>
                    </form>
                </div>
                </div>
            </div>
        @endforeach
    </div>
    <!-- Pagination Links -->
{{ $payments->links() }}
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

