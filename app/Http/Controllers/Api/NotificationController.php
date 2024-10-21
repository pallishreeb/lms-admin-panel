<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Notification;
use Hash;
use File;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user(); // Assuming user is authenticated
    
        $notifications = Notification::where('user_id', $user->id)
                                     ->orderBy('created_at', 'desc')
                                     ->get();
    
        return response()->json($notifications);
    }
    
}