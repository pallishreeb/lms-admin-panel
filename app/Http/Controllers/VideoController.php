<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Book;
use App\Models\Comment;
use App\Models\Reply;
use Redirect;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function create($bookId)
    {
        try {
            // Retrieve the book based on the provided $bookId
            $book = Book::findOrFail($bookId);
            
            // Pass the $bookId and $book to the view
            return view('videos.create', ['bookId' => $bookId, 'book' => $book]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while loading the video creation page: ' . $e->getMessage());
        }
    }

    public function store(Request $request, $bookId)
    {
        try {
            $book = Book::findOrFail($bookId);

            $video = new Video();
            $video->fill($request->all());
            $video->book_id = $book->id;
            $video->video_type = 'mp4';
            $video->position = 1;
            $video->save();

            return Redirect::route('books.edit', $book->id)->with('success', 'Video added successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while storing the video: ' . $e->getMessage());
        }
    }

    public function edit($bookId, $videoId)
    {
        try {
            $book = Book::findOrFail($bookId);
            $video = Video::findOrFail($videoId);
            return view('videos.edit', compact('bookId', 'video'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while loading the video edit page: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $bookId, $videoId)
    {
        try {
            $video = Video::findOrFail($videoId);
            $video->update($request->all());

            return Redirect::route('books.edit', $bookId)->with('success', 'Video updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while updating the video: ' . $e->getMessage());
        }
    }

    public function destroy($bookId, $videoId)
    {
        try {
            $video = Video::findOrFail($videoId);
            $video->delete();

            return Redirect::route('books.edit', $bookId)->with('success', 'Video deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while deleting the video: ' . $e->getMessage());
        }
    }

    public function upload(Request $request)
    {
        try {
            // Validate the request data, including file uploads
            $validatedData = $request->validate([
                'video' => 'required|mimetypes:video/mp4|max:512000', // 500 mb max file size 
            ]);
            
            // Upload video file and get the URL
            $videoUrl = null;
            if ($request->hasFile('video')) {
                $video = $request->file('video');
                $videoPath = 'book_videos/' . $video->getClientOriginalName();
                Storage::disk('s3')->put($videoPath, file_get_contents($video));
                $videoUrl = Storage::disk('s3')->url($videoPath);
            }

            return response()->json(['success' => true, 'videoUrl' => $videoUrl]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'An error occurred while uploading the video: ' . $e->getMessage()], 500);
        }
    }

    public function uploadPdf(Request $request)
    {
        try {
            // Validate the request data, including file uploads
            $validatedData = $request->validate([
                'attachment' => 'nullable|mimes:pdf,doc,docx|max:512000', // 500mb max file size as needed
            ]);
            
            // Upload attachment file and get the URL
            $attachmentUrl = null;
            if ($request->hasFile('attachment')) {
                $attachment = $request->file('attachment');
                $attachmentPath = 'book_video_attachments/' . $attachment->getClientOriginalName();
                Storage::disk('s3')->put($attachmentPath, file_get_contents($attachment));
                $attachmentUrl = Storage::disk('s3')->url($attachmentPath);
            }

            return response()->json(['success' => true, 'attachmentUrl' => $attachmentUrl]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'An error occurred while uploading the attachment: ' . $e->getMessage()], 500);
        }
    }

    public function comments()
    {
        try {
            // Get comments with video and replies, ordered by latest created_at for comments and replies
            $comments = Comment::with(['video', 'replies' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])->orderBy('created_at', 'desc')->get();
    
            return view('videos.comments', compact('comments'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while retrieving comments: ' . $e->getMessage());
        }
    }
    

    public function destroyComment(Comment $comment)
    {
        try {
            $comment->delete();
            return redirect()->back()->with('success', 'Comment deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while deleting the comment: ' . $e->getMessage());
        }
    }

    public function destroyReply(Reply $reply)
    {
        try {
            $reply->delete();
            return redirect()->back()->with('success', 'Reply deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while deleting the reply: ' . $e->getMessage());
        }
    }

    public function addReply(Request $request, Comment $comment)
    {
        $request->validate([
            'content' => 'required|string',
            'image' => 'nullable|image|max:30480', // Max 30MB for image
        ]);
    
        // Create a new reply
        $reply = new Reply();
        $reply->content = $request->input('content');
        $reply->user_id = auth()->id(); // Admin ID or authenticated user ID
        $reply->comment_id = $comment->id;
    
        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = 'comments-replies/' . $image->getClientOriginalName();
    
            // Store the image in AWS S3 or any other storage
            Storage::disk('s3')->put($imagePath, file_get_contents($image));
    
            // Save the image URL to the reply
            $imageUrl = Storage::disk('s3')->url($imagePath);
            $reply->image = $imageUrl;
        }
    
        // Save the reply
        $reply->save();
    
        return redirect()->back()->with('success', 'Reply added successfully.');
    }
    public function editReply(Request $request, Reply $reply)
    {
        // Validate the input
        $request->validate([
            'content' => 'required|string',
            'image' => 'nullable|image|max:30480', // Max 30MB for image
        ]);
    
        // Update the reply content
        $reply->content = $request->input('content');
    
        // Handle image upload (if a new one is provided)
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($reply->image) {
                Storage::disk('s3')->delete(basename($reply->image));
            }
    
            $image = $request->file('image');
            $imagePath = 'comments-replies/' . $image->getClientOriginalName();
    
            // Store new image in AWS S3
            Storage::disk('s3')->put($imagePath, file_get_contents($image));
    
            // Save the new image URL to the reply
            $imageUrl = Storage::disk('s3')->url($imagePath);
            $reply->image = $imageUrl;
        }
    
        // Save the updated reply
        $reply->save();
    
        return redirect()->back()->with('success', 'Reply updated successfully.');
    }
        

}
