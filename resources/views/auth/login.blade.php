<!-- resources/views/auth/login.blade.php -->

@extends('layouts.main')

@section('content')
<x-flash-message />
    <div class="flex mt-5  justify-center h-screen">
        <div class="w-1/2 max-w-md">
            <div class="bg-white rounded-lg shadow-md p-8">
                <h1 class="text-2xl font-bold mb-6 text-center">Sohoj Pora</h1>

                <form method="POST" action="{{route('authenticate')}}" class="space-y-4">
                    @csrf

                    <input type="hidden" name="device_id" id="device_id" value="">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700"><i class="fa-solid fa-user mr-2"></i>Email:</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="mt-1 p-2 w-full border rounded-md @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700"><i class="fa-solid fa-key mr-2"></i>Password:</label>
                        <input type="password" id="password" name="password" class="mt-1 p-2 w-full border rounded-md @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        {{-- <label class="inline-flex items-center">
                            <input type="checkbox" name="remember" class="form-checkbox" {{ old('remember') ? 'checked' : '' }}>
                            <span class="ml-2 text-sm">Remember me</span>
                        </label> --}}

                        <a href="{{route ('password.request')}}" class="text-blue-500 hover:underline">Forgot Password?</a>
                        <!-- <a href="{{route ('password.request')}}" class="text-blue-500 hover:underline">Forgot Password?</a> -->
                    </div>
                    {{-- <div class="flex items-center justify-between">
                        <a href="{{ url('/register')}}" class="text-blue-500 hover:underline">Don't have an account? Register here.</a>
                    </div> --}}
                    <div>
                        <button type="submit" class="w-full bg-yellow-600 text-white p-2 rounded-md hover:bg-yellow-800"><i class="fas fa-sign-in mr-2"></i>Login</button>
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