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
        // Retrieve the book based on the provided $bookId
        $book = Book::findOrFail($bookId);
        
        // Pass the $bookId and $book to the view
        return view('videos.create', ['bookId' => $bookId, 'book' => $book]);
    }

    public function store(Request $request, $bookId)
    {
        // $request->validate([
        //     'title' => 'required',
        //     'description' => 'required',
        //     'video_url' => 'required',
        //     'video_type' => 'required',
        //     'attachment_url' => 'required',
        //     'position' => 'required',
        //     'isPublished' => 'required',
        //     'isFree' => 'required',
        // ]);


        $book = Book::findOrFail($bookId);

        $video = new Video();
        $video->fill($request->all());
        $video->book_id = $book->id;
        $video->video_type = 'mp4';
        $video->position = 1;
        $video->save();

        return Redirect::route('books.edit', $book->id)->with('success', 'Video added successfully');
    }

    public function edit($bookId, $videoId)
    {
        $book = Book::findOrFail($bookId);
        $video = Video::findOrFail($videoId);
        return view('videos.edit', compact('bookId', 'video'));
    }
    

    public function update(Request $request, $bookId, $videoId)
    {
       
        $video = Video::findOrFail($videoId);
        $video->update($request->all());

        return Redirect::route('books.edit', $bookId)->with('success', 'Video updated successfully');
    }

    public function destroy($bookId, $videoId)
    {
        $video = Video::findOrFail($videoId);
        $video->delete();

        return Redirect::route('books.edit', $bookId)->with('success', 'Video deleted successfully');
    }

    public function upload(Request $request)
    {
        // Validate the request data, including file uploads
        $validatedData = $request->validate([
            'video' => 'required|mimetypes:video/mp4|max:512000', // Adjust max file size as needed
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
    }
    public function uploadPdf(Request $request)
    {
        // Validate the request data, including file uploads
        $validatedData = $request->validate([
            'attachment' => 'nullable|mimes:pdf,doc,docx|max:512000', // Adjust max file size as needed
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
    }
    
    public function comments()
    {
        $comments = Comment::with('video', 'replies')->get();

        return view('videos.comments', compact('comments'));
    }
    public function destroyComment(Comment $comment)
    {
        $comment->delete();

        return redirect()->back()->with('success', 'Comment deleted successfully');
    }
    public function destroyReply(Reply $reply)
    {
        $reply->delete();

        return redirect()->back()->with('success', 'Reply deleted successfully');
    }
}
