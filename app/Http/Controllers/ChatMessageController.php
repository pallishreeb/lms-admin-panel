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
    $request->validate([
        'message' => 'required|string',
        'image' => 'nullable|image|max:30480',
        'audio' => 'nullable|file|max:50480',
    ]);

    $admin = auth()->user();

    $message = new ChatMessage([
        'message' => $request->input('message'),
        'user_id' => $user->id,
        'admin_id' => $admin->id,
    ]);

    // Handle image upload
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

    // Handle audio upload
    if ($request->hasFile('audio')) {
        try {
            $audio = $request->file('audio');
            $audioName = 'chat_audios/' . $audio->getClientOriginalName();
            Storage::disk('s3')->put($audioName, file_get_contents($audio));
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