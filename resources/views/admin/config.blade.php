<!-- resources/views/admin/config.blade.php -->
@extends('layouts.app')

@section('content')
<div class="max-w-2xl mt-8">
    <h2 class="text-xl font-bold mb-4">Application Configurations</h2>

    <ul class="list-disc pl-4 mb-4">
        @foreach($configurations as $config)
            <li class="mb-2">{{ $config->config_key }}: {{ $config->config_value }}</li>
        @endforeach
    </ul>

    <form method="post" action="{{ route('admin.updateNotificationPreference') }}" class="mb-4">
        @csrf
        <label for="notification_preference" class="block text-sm font-medium text-gray-700">Notification Preference:</label>
        <select name="notification_preference" id="notification_preference" class="mt-1 p-2 border border-gray-300 rounded-md">
            <option value="email" {{ $notificationPreference === 'email' ? 'selected' : '' }}>Email</option>
            <option value="sms" {{ $notificationPreference === 'sms' ? 'selected' : '' }}>SMS</option>
        </select>
        <button type="submit" class="mt-2 px-4 py-2 bg-yellow-500 text-white rounded-md">Update Preference</button>
    </form>
</div>
@endsection
