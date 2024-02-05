<!-- resources/views/auth/register.blade.php -->

@extends('layouts.main')

@section('content')
    <div class="flex mt-2 justify-center h-screen">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-lg shadow-md p-8">
                <h1 class="text-2xl font-bold mb-6">Register</h1>

                <form method="POST" action="{{ route('store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="device_id" id="device_id" value="">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700"><i class="fa-solid fa-user mr-1"></i> Name:</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" class="mt-1 p-2 w-full border rounded-md @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700"><i class="fa-solid fa-envelope mr-1"></i>Email:</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="mt-1 p-2 w-full border rounded-md @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="Mobile" class="block text-sm font-medium text-gray-700"><i class="fa-solid fa-phone mr-1"></i>Mobile:</label>
                        <input type="text" id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}" class="mt-1 p-2 w-full border rounded-md @error('mobile_number') border-red-500 @enderror">
                        @error('mobile_number')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700"><i class="fa-solid fa-key mr-1"></i>Password:</label>
                        <input type="password" id="password" name="password" class="mt-1 p-2 w-full border rounded-md @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700"><i class="fa-solid fa-key mr-1"></i>Confirm Password:</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="mt-1 p-2 w-full border rounded-md">
                    </div>
                    <div class="flex items-center justify-between">

                        <a href="/login" class="text-blue-500 hover:underline">Already have account? Login.</a>
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-yellow-600 text-white p-2 rounded-md hover:bg-yellow-800">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
    // Function to generate a pseudo-unique identifier
    function generateDeviceId() {
        const date = new Date().toISOString().replace(/[^\d]/g, '');
        const random = Math.random().toString(36).substr(2, 9);
        return date + random;
    }

    // Use this function to set the device ID on your form
    function setDeviceId() {
        const deviceIdInput = document.getElementById('device_id');
        if (deviceIdInput) {
            const storedDeviceId = localStorage.getItem('sp_device_id');

            if (storedDeviceId) {
                // Use the stored device ID if available
                deviceIdInput.value = storedDeviceId;
            } else {
                // Generate a new device ID and store it
                const newDeviceId = generateDeviceId();
                localStorage.setItem('sp_device_id', newDeviceId);
                deviceIdInput.value = newDeviceId;
            }
        }
    }

    // Call setDeviceId when the page loads
    setDeviceId();
</script>
@endsection