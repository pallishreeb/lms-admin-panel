<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatMessage;
use App\Models\User;
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
    ]);

    $admin = auth()->user();


    $message = new ChatMessage([
        'message' => $request->input('message'),
        'user_id' => $user->id,
        'admin_id' => $admin->id,
    ]);

    $message->save();

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