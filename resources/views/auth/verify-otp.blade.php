<!-- resources/views/auth/verify-otp.blade.php -->

@extends('layouts.main') <!-- Assuming you have a layout file -->

@section('content')
<x-flash-message />
    <div class="flex mt-8 justify-center items-center ">
        <div class="w-full  max-w-md">
            <div class="bg-white rounded-lg shadow-md p-8">
            <h2 class="text-2xl font-bold mb-4 text-center ">Verify OTP</h2>

            <form method="post" action="{{ route('verify.otp') }}">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <div class="mb-4">
                    <label for="otp" class="block text-gray-700 text-sm font-bold">OTP:</label>
                    <input type="text" name="otp" id="otp" class="border-2 border-gray-300 p-2 w-full" required>
                </div>
                <div class="text-center">
                    <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded-md">Verify OTP</button>
                </div>
            </form>
            </div>
        </div>
    </div>
@endsection
