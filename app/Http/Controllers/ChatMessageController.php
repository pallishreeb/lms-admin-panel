<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
class ChatMessageController extends Controller
{

    public function index()
{
    $userIds = ChatMessage::distinct('user_id')->pluck('user_id');

    // Fetch users based on the unique user IDs
    $users = User::whereIn('id', $userIds)->get();

    return view('chats.index', compact('users'));
}

public function showUserChats(User $user)
{
    $chats = ChatMessage::where('user_id', $user->id)->get();

    return view('chats.show', compact('user', 'chats'));
}

public function reply(User $user, Request $request)
{
    // Print the entire request for debugging
    // dd($request->all());  // Only use this for debugging if needed

    $request->validate([
        'message' => 'required|string',
        'image' => 'nullable|image|max:30480',
        'audio_base64' => 'nullable|string',
    ]);

    $admin = auth()->user();

    $message = new ChatMessage([
        'message' => $request->input('message'),
        'user_id' => $user->id,
        'admin_id' => $admin->id,
    ]);

    // Handle image upload (if included)
    if ($request->hasFile('image')) {
        try {
            $image = $request->file('image');
            $imageName = 'chat_images/' . $image->getClientOriginalName();
            Storage::disk('s3')->put($imageName, file_get_contents($image));
            $message->image = Storage::disk('s3')->url($imageName);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Image upload failed: ' . $e->getMessage());
        }
    }

    // Handle audio upload from base64
    if ($request->input('audio_base64')) {
        try {
            $audio_base64 = $request->input('audio_base64');
            $audioData = base64_decode($audio_base64);
            
            // Define the audio file name and path
            $audioName = 'chat_audios/audio_' . time() . '.wav';  // Assuming the format is .wav, change as needed

            // Save the audio to S3
            Storage::disk('s3')->put($audioName, $audioData);
            
            // Set the audio URL in the message
            $message->audio = Storage::disk('s3')->url($audioName);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Audio upload failed: ' . $e->getMessage());
        }
    }

    // Save the message and handle potential errors
    try {
        $message->save();
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to save message: ' . $e->getMessage());
    }

    return redirect()->route('user-chats', ['user' => $user->id])
        ->with('success', 'Reply sent successfully');
}

public function showUserMessages(User $user)
{
    $messages = ChatMessage::where('user_id', $user->id)->get();

    return view('filament.pages.view-chat', compact('user', 'messages'));
}

public function delete($id)
    {
        try {
            $chatMessage = ChatMessage::findOrFail($id);
            $chatMessage->delete();

            return redirect()->back()->with('success', 'Message deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting message');
        }
    }

}