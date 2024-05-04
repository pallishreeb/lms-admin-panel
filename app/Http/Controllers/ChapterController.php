<?php
// app\Http\Controllers\ChapterController.php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Course;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChapterController extends Controller
{
    public function create($courseId)
    {
        // Fetch the course by ID
        $course = Course::findOrFail($courseId);
    
        // Pass the course to the view
        return view('chapters.create', compact('course'));
    }
    
    public function store(Request $request)
    {
        // Validate the request data, including file uploads
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'videoUrl' => 'required|string',
            'attachmentUrl' => 'nullable|string',
            // 'attachment' => 'nullable|mimes:pdf,doc,docx|max:102400', // Adjust max file size and allowed file types as needed
            // 'position' => 'required|integer',
            // 'isPublished' => 'required|boolean',
            // 'isFree' => 'required|boolean',
            'courseId' => 'required|exists:courses,id',
        ]);
       
        //    $attachmentUrl = null;
        //    if ($request->hasFile('attachment')) {
        //        $attachment = $request->file('attachment');
        //        $attachmentPath = 'attachments/' . $attachment->getClientOriginalName();
        //        Storage::disk('s3')->put($attachmentPath, file_get_contents($attachment));
        //        $attachmentUrl = Storage::disk('s3')->url($attachmentPath);
        //    }

        // Save the chapter with the file URLs
        $chapter = Chapter::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'video_url' => $validatedData['videoUrl'],
            'attachment_url' => $validatedData['attachmentUrl'],
            'position' => 0,
            // 'isPublished' => $validatedData['isPublished'],
            // 'isFree' => $validatedData['isFree'],
            'isPublished' => 1,
            'isFree' => 1,
            'course_id' => $validatedData['courseId'],
            'video_type' => 'mp4'

        ]);


        // Update the chapters array in the Course model
        $course = Course::findOrFail($validatedData['courseId']);

        // Initialize chapters array if it's initially null
        $course->chapters = $course->chapters ?? [];

        $course->chapters = array_merge($course->chapters, [$chapter->id]);
        $course->save();
        $categories = Category::all();
        // return view('courses.edit', compact('course', 'categories'));
        return redirect()->route('courses.edit', ['course' => $validatedData['courseId'], 'categories'=> $categories])
              ->with('success', 'Chapter created successfully');

       // return redirect()->route('courses.index')->with('success', 'Chapter created successfully');
    }

    public function edit(Chapter $chapter)
    {
        // You may need to load courses here if needed
        $courses = Course::all();
        return view('chapters.edit', compact('chapter', 'courses'));
    }

    public function update(Request $request, Chapter $chapter)
    {
        // Validate the request data, including file uploads
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            // 'position' => 'required|integer',
            // 'isPublished' => 'required|boolean',
            // 'isFree' => 'required|boolean',
            'courseId' => 'required|exists:courses,id',
            // 'attachment' => 'nullable|mimes:pdf,doc,docx|max:10240', // Adjust max file size and allowed file types as needed
        
        ]);

           $videoUrl = $chapter->video_url;
            // Check if the request has the videoUrl field with a string value
           if ($request->filled('videoUrl') && is_string($request->videoUrl)) {
              $videoUrl  = $request->videoUrl;
            }

            $attachmentUrl = $chapter->attachment_url;
            // Check if the request has the videoUrl field with a string value
           if ($request->filled('attachmentUrl') && is_string($request->attachmentUrl)) {
              $attachmentUrl  = $request->attachmentUrl;
            }
                // $attachmentUrl = $chapter->attachment_url;
                // // If a new attachment file is provided, upload and update the URL
                // if ($request->hasFile('attachment')) {
                //     $attachment = $request->file('attachment');
                //     $attachmentPath = 'attachments/' . $attachment->getClientOriginalName();
                //     Storage::disk('s3')->put($attachmentPath, file_get_contents($attachment));
                //     $attachmentUrl = Storage::disk('s3')->url($attachmentPath);
                // }


        // Update the chapter with the new data
        $chapter->update([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'video_url' => $videoUrl,
            // 'position' => $validatedData['position'],
            // 'isPublished' => $validatedData['isPublished'],
            // 'isFree' => $validatedData['isFree'],
            'course_id' => $validatedData['courseId'],
            'video_type' => 'mp4',
            'attachment_url' => $attachmentUrl,
        ]);


        // Update the chapters array in the Course model
        $course = Course::findOrFail($validatedData['courseId']);
        $categories = Category::all();
        return redirect()->route('courses.edit', ['course' => $validatedData['courseId'], 'categories'=> $categories])
              ->with('success', 'Chapter Updated successfully');
    }

    public function destroy(Chapter $chapter)
    {
        $courseId = $chapter->course_id;
        $chapter->delete();
        $course = Course::findOrFail($courseId);
        $categories = Category::all();
        return redirect()->route('courses.edit', ['course' => $courseId, 'categories'=> $categories])
              ->with('success', 'Chapter Deleted successfully');
        // return redirect()->route('courses.index')->with('success', 'Chapter deleted successfully!');
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
           $videoPath = 'videos/' . $video->getClientOriginalName();
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
           $attachmentPath = 'attachments/' . $attachment->getClientOriginalName();
           Storage::disk('s3')->put($attachmentPath, file_get_contents($attachment));
           $attachmentUrl = Storage::disk('s3')->url($attachmentPath);
       }
 
       return response()->json(['success' => true, 'attachmentUrl' => $attachmentUrl]);
    }
    

    public function uploadPdfBook(Request $request)
    {
        // Validate the request data, including file uploads
        $validatedData = $request->validate([
            'pdf_book' => 'required|mimes:pdf|max:512000',// Adjust max file size as needed
        ]);
       // Upload attachment file and get the URL
       $attachmentUrl = null;
       if ($request->hasFile('pdf_book')) {
           $attachment = $request->file('pdf_book');
           $attachmentPath = 'pdf_books/' . $attachment->getClientOriginalName();
           Storage::disk('s3')->put($attachmentPath, file_get_contents($attachment));
           $attachmentUrl = Storage::disk('s3')->url($attachmentPath);
       }
 
       return response()->json(['success' => true, 'attachmentUrl' => $attachmentUrl]);
    }
}
