<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
        public function getMessages(Request $request, $user_id)
        {
            try {
                // Use $user_id as needed in your logic
                $messages = ChatMessage::with('user')
                    ->where('user_id', $user_id)
                    ->orderBy('created_at', 'asc')
                    ->get();
        
                return response()->json(['messages' => $messages]);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error fetching messages'], 500);
            }
        }
  
// Function to send a new chat message
public function sendMessage(Request $request)
{
    // Validate incoming request
    $request->validate([
        'message' => 'required|string',
        'image' => 'nullable|image|max:30480', // Image validation
        'audio' => 'nullable|file|max:50480', // Audio validation
    ]);

    try {
        // Create a new chat message instance
        $message = new ChatMessage([
            'message' => $request->input('message'),
            'user_id' => $request->input('user_id'),
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $chatImage = $request->file('image');
            $imageName = 'chat_messages_pics/' . $chatImage->getClientOriginalName();
            Storage::disk('s3')->put($imageName, file_get_contents($chatImage));
            $message->image = Storage::disk('s3')->url($imageName);
        }

        // Handle audio upload
        if ($request->hasFile('audio')) {
            $audioFile = $request->file('audio');
            $audioName = 'chat_messages_audios/' . $audioFile->getClientOriginalName();
            Storage::disk('s3')->put($audioName, file_get_contents($audioFile));
            $message->audio = Storage::disk('s3')->url($audioName);
        }

        // Save the message
        $message->save();

        return response()->json(['message' => 'Message sent successfully']);
    } catch (\Exception $e) {
        // Handle any exceptions that occur during the process
        return response()->json(['error' => 'Failed to send message: ' . $e->getMessage()], 500);
    }
}




        //delete a message
        public function deleteMessage(Request $request, $id)
{
    try {
        $user = Auth::user();
        $message = ChatMessage::findOrFail($id);

        // Use a database transaction to ensure consistency
        DB::beginTransaction();

        $message->delete();

        // Commit the transaction
        DB::commit();

        return response()->json(['message' => 'Message deleted successfully']);
    } catch (\Exception $e) {
        // Rollback the transaction in case of an error
        DB::rollBack();

        return response()->json(['error' => 'Error deleting message'], 500);
    }
}
}
