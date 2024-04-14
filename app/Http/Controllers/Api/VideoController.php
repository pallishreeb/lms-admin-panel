<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Video;
use App\Models\Comment;
use App\Models\Reply;

class VideoController extends Controller
{
    public function toggleLike(Request $request, $videoId)
    {
        $video = Video::findOrFail($videoId);
        $userId = auth()->id();
    
        // Check if the user has already liked the video
        $liked = $video->likesDislikes()->where('user_id', $userId)->where('is_like', true)->exists();
    
        if ($liked) {
            // If already liked, remove the like
            $video->likesDislikes()->where('user_id', $userId)->delete();
            return response()->json(['message' => 'Like removed'], 200);
        } else {
            // If already disliked, remove the dislike
            $disliked = $video->likesDislikes()->where('user_id', $userId)->where('is_like', false)->exists();
            if ($disliked) {
                $video->likesDislikes()->where('user_id', $userId)->delete();
            }
    
            // Add like
            $video->likesDislikes()->create([
                'user_id' => $userId,
                'is_like' => true,
            ]);
    
            return response()->json(['message' => 'Video liked'], 200);
        }
    }
    
    public function toggleDislike(Request $request, $videoId)
    {
        $video = Video::findOrFail($videoId);
        $userId = auth()->id();

        // Check if the user has already disliked the video
        $disliked = $video->likesDislikes()->where('user_id', $userId)->where('is_like', false)->exists();

        if ($disliked) {
            // If already disliked, remove the dislike
            $video->likesDislikes()->where('user_id', $userId)->delete();
            return response()->json(['message' => 'Dislike removed'], 200);
        } else {
            // If already liked, remove the like
            $liked = $video->likesDislikes()->where('user_id', $userId)->where('is_like', true)->exists();
            if ($liked) {
                $video->likesDislikes()->where('user_id', $userId)->delete();
            }

            // Add dislike
            $video->likesDislikes()->create([
                'user_id' => $userId,
                'is_like' => false,
            ]);

            return response()->json(['message' => 'Video disliked'], 200);
        }
    }


    public function addComment(Request $request, $videoId)
    {
        $request->validate([
            'content' => 'required|string',
            'image' => 'nullable|image|max:20480', // Max 2MB
        ]);

        $video = Video::findOrFail($videoId);

        $comment = new Comment();
        $comment->content = $request->input('content');
        $comment->user_id = auth()->id();
        $comment->video_id = $video->id;

        // Upload image if provided
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = 'book-videos-comments/' . $image->getClientOriginalName();
            Storage::disk('s3')->put($imagePath, file_get_contents($image));
            $imageUrl = Storage::disk('s3')->url($imagePath);
            $comment->image = $imageUrl;
        }

        $comment->save();

        return response()->json(['message' => 'Comment added successfully'], 201);
    }

    public function deleteComment(Request $request, $commentId)
    {
        $comment = Comment::findOrFail($commentId);

        // Check if the authenticated user owns the comment
        if ($comment->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Delete comment
        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully'], 200);
    }

    public function addReply(Request $request, $commentId)
    {
        $request->validate([
            'content' => 'required|string',
            'image' => 'nullable|image|max:20480', // Max 2MB
        ]);

        $comment = Comment::findOrFail($commentId);

        $reply = new Reply();
        $reply->content = $request->input('content');
        $reply->user_id = auth()->id();
        $reply->comment_id = $comment->id;

        // Upload image if provided
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = 'book-videos-comments/' . $image->getClientOriginalName();
            Storage::disk('s3')->put($imagePath, file_get_contents($image));
            $imageUrl = Storage::disk('s3')->url($imagePath);
            $reply->image = $imageUrl;
        }

        $reply->save();

        return response()->json(['message' => 'Reply added successfully'], 201);
    }

    public function deleteReply(Request $request, $replyId)
    {
        $reply = Reply::findOrFail($replyId);

        // Check if the authenticated user owns the reply
        if ($reply->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Delete reply
        $reply->delete();

        return response()->json(['message' => 'Reply deleted successfully'], 200);
    }

    public function getVideosForBook($bookId)
    {
        $videos = Video::where('book_id', $bookId)
                       ->withCount('comments') // Include comments count
                       ->get();
    
        // Calculate likes and dislikes counts using accessors
        $videos->each(function ($video) {
            $video->likes_count = $video->likes_count;
            $video->dislikes_count = $video->dislikes_count;
        });
    
        return response()->json($videos);
    }
    
    public function getCommentsWithReplies($videoId)
    {
        $video = Video::findOrFail($videoId);

        // Eager load comments with their replies
        $comments = Comment::with('replies')->where('video_id', $video->id)->get();

        return response()->json($comments);
    }
    public function editComment(Request $request, $commentId)
    {
        $request->validate([
            'content' => 'required|string',
            'image' => 'nullable|image|max:20480', // Max 2MB
        ]);

        $comment = Comment::findOrFail($commentId);

        // Check if the authenticated user owns the comment
        if ($comment->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Update comment fields
        $comment->content = $request->input('content');

        // Upload new image if provided
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = 'book-videos-comments/' . $image->getClientOriginalName();
            Storage::disk('s3')->put($imagePath, file_get_contents($image));
            $imageUrl = Storage::disk('s3')->url($imagePath);
            $comment->image = $imageUrl;
        }

        $comment->save();

        return response()->json(['message' => 'Comment updated successfully'], 200);
    }
    public function getComments(Request $request, $videoId)
    {
        // Get the sorting order from the request
        $order = $request->input('order', 'asc'); // Default to ascending order if not specified
        $video = Video::findOrFail($videoId);

        // Eager load comments with their replies
        $comments = Comment::with('replies')->where('video_id', $video->id)->orderBy('created_at', $order)->get();
        // Query comments for the specified video and order them accordingly
        // $comments = Comment::where('video_id', $videoId)
        //     ->orderBy('created_at', $order)
        //     ->get();

        return response()->json($comments);
    }
    public function editReply(Request $request, $replyId)
    {
        $request->validate([
            'content' => 'required|string',
            'image' => 'nullable|image|max:20480', // Max 2MB
        ]);

        $reply = Reply::findOrFail($replyId);

        // Check if the authenticated user owns the reply
        if ($reply->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Update reply fields
        $reply->content = $request->input('content');

        // Upload new image if provided
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = 'book-videos-comments/' . $image->getClientOriginalName();
            Storage::disk('s3')->put($imagePath, file_get_contents($image));
            $imageUrl = Storage::disk('s3')->url($imagePath);
            $reply->image = $imageUrl;
        }

        $reply->save();

        return response()->json(['message' => 'Reply updated successfully'], 200);
    }

    public function getRepliesForComment(Request $request, $commentId)
    {
        $comment = Comment::findOrFail($commentId);

        // Get all replies for the specified comment
        $replies = $comment->replies;

        return response()->json(['replies' => $replies], 200);
    }
   
//     public function getVideoDetails(Request $request)
// {
//     $request->validate([
//         'video_url' => 'required|url', // Ensure video_url is present and a valid URL
//     ]);
//     // Decode the URL parameter
//     $videoUrl = $request->input('video_url');

//     // Find the video by its URL
//     $video = Video::where('video_url', $videoUrl)->first();

//     // If video not found, return 404 response
//     if (!$video) {
//         return response()->json(['error' => 'Video not found'], 404);
//     }

//     // Load the related data along with the video
//     $video->load('book', 'comments.user', 'comments.replies.user', 'likes', 'likesDislikes');

//     return response()->json(['video' => $video]);
// }


public function getVideoDetails(Request $request)
{
    $request->validate([
        'video_url' => 'required|url', // Ensure video_url is present and a valid URL
    ]);

    // Decode the URL parameter
    $videoUrl = $request->input('video_url');

    // Find the video by its URL
    $video = Video::where('video_url', $videoUrl)->first();

    // If video not found, return 404 response
    if (!$video) {
        return response()->json(['error' => 'Video not found'], 404);
    }

    // Load the related data along with the video
    $video->load(['book', 'comments' => function ($query) {
        $query->orderByDesc('created_at'); // Order comments from latest to oldest
        $query->with('user', 'replies.user');
    }, 'likes','dislikes']);

    // Calculate the dislikes count
    $dislikesCount = $video->likesDislikes()->where('is_like', false)->count();

    // Add the likes and dislikes count to the video object
    $video->likesCount = $video->getLikesCountAttribute();
    $video->dislikesCount = $video->getDislikesCountAttribute();

    return response()->json(['video' => $video]);
}
}
