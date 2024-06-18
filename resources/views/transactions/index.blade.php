@extends('layouts.app')

@section('content')
    <div class="mt-2">

        <div class="flex justify-between items-center mb-4">
            <h1 class="text-xl font-semibold">Filter Payment</h1>
        </div>

        <!-- Filter Form -->
        <form action="{{ route('transactions.index') }}" method="get" class="mb-4">
            <div class="grid grid-cols-2 gap-4 mb-4">
                <select name="student_id" class="border p-2">
                    <option value="">Select Student</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>{{ $student->name }}</option>
                    @endforeach
                </select>

                <select name="category_id" class="border p-2">
                    <option value="">Select Class</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('category_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                    @endforeach
                </select>

                <input type="date" name="payment_date" placeholder="Payment Date" value="{{ request('payment_date') }}" class="border p-2">

                <select name="payment_status" class="border p-2">
                    <option value="">Select Status</option>
                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('payment_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('payment_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <select name="payment_method" class="border p-2">
                    <option value="">Select Payment Method</option>
                    @foreach($paymentMethods as $paymentMethod)
                        <option value="{{ $paymentMethod->payment_method }}" {{ request('payment_method') == $paymentMethod->payment_method ? 'selected' : '' }}>{{ $paymentMethod->payment_method }}</option>
                    @endforeach
                </select>
                {{-- <input type="text" name="division" placeholder="Division" value="{{ request('division') }}" class="border p-2">
                <input type="text" name="district" placeholder="District" value="{{ request('district') }}" class="border p-2">
                <input type="text" name="upazila" placeholder="Upazila" value="{{ request('upazila') }}" class="border p-2"> --}}
                <!-- Division Dropdown -->
        <div>
            <label for="division">Division</label>
            <select name="division" id="division" class="border border-gray-300 rounded-md p-1 w-full">
                <option value="">Select Division</option>
                @foreach($divisions as $division)
                    <option value="{{ $division['division'] }}">{{ $division['division'] }}</option>
                @endforeach
            </select>
        </div>

        <!-- District Dropdown -->
        <div>
            <label for="district">District(First select Division)</label>
            <select name="district" id="district" class="border border-gray-300 rounded-md p-1 w-full" disabled>
                <option value="">Select District</option>
            </select>
        </div>

        <!-- Upazila Dropdown -->
        <div>
            <label for="upazila">Upazila(First select District)</label>
            <select name="upazila" id="upazila" class="border border-gray-300 rounded-md p-1 w-full" disabled>
                <option value="">Select Upazila</option>
            </select>
        </div>
                <input type="text" name="school_name" placeholder="School Name" value="{{ request('school_name') }}" class="border p-2">
                <input type="number" name="amount" placeholder="Payment Amount" value="{{ request('amount') }}" class="border p-2">
                
            </div>
            <div>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md">Filter</button>
                <a href="{{ route('transactions.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md">Clear Filter</a>
            </div>
        </form>

        <button class="bg-yellow-500 text-white px-4 py-2 rounded-md mb-2 mt-2">Analog Payments List</button>
        <table class="mt-4 w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-gray-300 px-4 py-2">ID</th>
                    <th class="border border-gray-300 px-4 py-2">Payment Screenshot</th>
                    <th class="border border-gray-300 px-8 py-2">Student Name</th>
                    {{-- <th class="border border-gray-300 px-8 py-2">Student Email</th> --}}
                    <th class="border border-gray-300 px-8 py-2">Mobile Number</th>
                    <th class="border border-gray-300 px-4 py-2">Class</th>
                    <th class="border border-gray-300 px-4 py-2">Payment Method</th>
                    <th class="border border-gray-300 px-4 py-2">Payment Number</th>
                    <th class="border border-gray-300 px-4 py-2">Amount</th>
                    <th class="border border-gray-300 px-4 py-2">Payment Date</th>
                    <th class="border border-gray-300 px-4 py-2">Payment Time</th>
                    <th class="border border-gray-300 px-4 py-2">Division</th>
                    <th class="border border-gray-300 px-4 py-2">District</th>
                    <th class="border border-gray-300 px-4 py-2">Upazila</th>
                    <th class="border border-gray-300 px-8 py-2">School Name</th>                   
                    <th class="border border-gray-300 px-4 py-2">Status</th>
                  
                    <th class="border border-gray-300 px-4 py-2">Update Status</th>
                    <th class="border border-gray-300 px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($paymentDetails as $paymentDetail)
                    <tr class="hover:bg-gray-100">
                        <td class="border border-gray-300 px-4 py-2">{{ $paymentDetail->id }}</td>
                        <td class="border border-gray-300 px-4 py-2">
                            @if ($paymentDetail->payment_screenshot)
                            <a href="{{ $paymentDetail->payment_screenshot }}" target="_blank">
                                <img src="{{ $paymentDetail->payment_screenshot }}" alt="Screenshot" class="w-20 h-20 object-cover">
                            </a>
                            @else
                                <span class="text-gray-500">NA</span>
                           @endif
                        </td>
                        <td class="border border-gray-300 px-1 py-2">{{ $paymentDetail->student->name ?? 'NA'}}</td>
                        {{-- <td class="border border-gray-300 px-1 py-2">{{ $paymentDetail->student->email ?? 'NA'}}</td>  --}}
                        <td class="border border-gray-300 px-1 py-2">{{ $paymentDetail->mobile_number ?? 'NA'}}</td>
                        <td class="border border-gray-300 px-1 py-2">{{ $paymentDetail->category->name ?? 'NA'}}</td> 
                        <td class="border border-gray-300 px-1 py-2">{{ $paymentDetail->payment_method ?? 'NA'}}</td>
                        <td class="border border-gray-300 px-1 py-2">{{ $paymentDetail->payment_number ?? 'NA'}}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $paymentDetail->amount ?? 'NA' }}</td>
                        <td class="border border-gray-300 px-2 py-2">{{ $paymentDetail->created_at->format('Y-m-d') }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $paymentDetail->created_at->format('H:i:s') }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $paymentDetail->division ?? 'NA' }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $paymentDetail->district ?? 'NA' }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $paymentDetail->upazilla  ?? 'NA' }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $paymentDetail->school_name ?? 'NA'}}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $paymentDetail->status }}</td>
                      
                        <td class="border border-gray-300 px-2 py-2">
                            <!-- Form to update status -->
                            <form action="{{ route('admin.payments.updateStatus', $paymentDetail->id) }}" method="post" class="flex flex-col">
                                @csrf
                                @method('put')
                                <select name="status" class="border border-gray-300 rounded-md p-1">
                                    <option value="">Select Status</option>
                                    <option value="approved">Approve</option>
                                    <option value="rejected">Reject</option>
                                </select>
                                <button type="submit" class="bg-indigo-600 text-white px-4 py-1 mt-1 rounded-md">Update</button>
                            </form>
                        </td>
                        <td class="border border-gray-300 px-4 py-2">
                            <form action="{{ route('payments.destroy', $paymentDetail->id) }}" method="POST" onsubmit="return confirmDelete(event)" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="13" class="border border-gray-300 px-4 py-2 text-center">No payment details found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <!-- Pagination Links -->
        <div class="mt-4">
            {{ $paymentDetails->links() }}
        </div>
    </div>
    <script>
        document.getElementById('division').addEventListener('change', function() {
            const division = this.value;
            const districtSelect = document.getElementById('district');
            const upazilaSelect = document.getElementById('upazila');
        
            if (division) {
                fetch(`https://bdapis.com/api/v1.2/division/${division}`)
                    .then(response => response.json())
                    .then(data => {
                        districtSelect.disabled = false;
                        districtSelect.innerHTML = '<option value="">Select District</option>';
                        upazilaSelect.disabled = true;
                        upazilaSelect.innerHTML = '<option value="">Select Upazila</option>';
        
                        data.data.forEach(district => {
                            districtSelect.innerHTML += `<option value="${district.district}">${district.district}</option>`;
                        });
                    });
            } else {
                districtSelect.disabled = true;
                districtSelect.innerHTML = '<option value="">Select District</option>';
                upazilaSelect.disabled = true;
                upazilaSelect.innerHTML = '<option value="">Select Upazila</option>';
            }
        });
        
        document.getElementById('district').addEventListener('change', function() {
            const division = document.getElementById('division').value;
            const district = this.value;
            const upazilaSelect = document.getElementById('upazila');
        
            if (division && district) {
                fetch(`https://bdapis.com/api/v1.2/division/${division}`)
                    .then(response => response.json())
                    .then(data => {
                        const selectedDistrict = data.data.find(d => d.district === district);
                        if (selectedDistrict) {
                            upazilaSelect.disabled = false;
                            upazilaSelect.innerHTML = '<option value="">Select Upazila</option>';
        
                            selectedDistrict.upazilla.forEach(upazila => {
                                upazilaSelect.innerHTML += `<option value="${upazila}">${upazila}</option>`;
                            });
                        }
                    });
            } else {
                upazilaSelect.disabled = true;
                upazilaSelect.innerHTML = '<option value="">Select Upazila</option>';
            }
        });
        </script>
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
