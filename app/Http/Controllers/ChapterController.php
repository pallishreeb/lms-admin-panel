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
            'video' => 'required|mimetypes:video/mp4|max:102400', // Adjust max file size as needed
            'attachment' => 'nullable|mimes:pdf,doc,docx|max:102400', // Adjust max file size and allowed file types as needed
            'position' => 'required|integer',
            'isPublished' => 'required|boolean',
            'isFree' => 'required|boolean',
            'courseId' => 'required|exists:courses,id',
        ]);
       // Upload video file and get the URL
       $videoUrl = null;
       if ($request->hasFile('video')) {
           $video = $request->file('video');
           $videoPath = 'videos/' . $video->getClientOriginalName();
           Storage::disk('s3')->put($videoPath, file_get_contents($video));
           $videoUrl = Storage::disk('s3')->url($videoPath);
       }
       
       $attachmentUrl = null;
       if ($request->hasFile('attachment')) {
           $attachment = $request->file('attachment');
           $attachmentPath = 'attachments/' . $attachment->getClientOriginalName();
           Storage::disk('s3')->put($attachmentPath, file_get_contents($attachment));
           $attachmentUrl = Storage::disk('s3')->url($attachmentPath);
       }

        // Save the chapter with the file URLs
        $chapter = Chapter::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'video_url' => $videoUrl,
            'attachment_url' => $attachmentUrl,
            'position' => $validatedData['position'],
            'isPublished' => $validatedData['isPublished'],
            'isFree' => $validatedData['isFree'],
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
            'video' => 'nullable|mimetypes:video/mp4|max:102400', // Adjust max file size as needed
            'attachment' => 'nullable|mimes:pdf,doc,docx|max:10240', // Adjust max file size and allowed file types as needed
            'position' => 'required|integer',
            'isPublished' => 'required|boolean',
            'isFree' => 'required|boolean',
            'courseId' => 'required|exists:courses,id',
        ]);

           $videoUrl = $chapter->video_url;
                // If a new video file is provided, upload and update the URL
                if ($request->hasFile('video')) {
                    $video = $request->file('video');
                    $videoPath = 'videos/' . $video->getClientOriginalName();
                    Storage::disk('s3')->put($videoPath, file_get_contents($video));
                    $videoUrl = Storage::disk('s3')->url($videoPath);
                    $chapter->update(['video_url' => $videoUrl]);
                }
                $attachmentUrl = $chapter->attachment_url;
                // If a new attachment file is provided, upload and update the URL
                if ($request->hasFile('attachment')) {
                    $attachment = $request->file('attachment');
                    $attachmentPath = 'attachments/' . $attachment->getClientOriginalName();
                    Storage::disk('s3')->put($attachmentPath, file_get_contents($attachment));
                    $attachmentUrl = Storage::disk('s3')->url($attachmentPath);
                }


        // Update the chapter with the new data
        $chapter->update([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'video_url' => $videoUrl,
            'attachment_url' => $attachmentUrl,
            'position' => $validatedData['position'],
            'isPublished' => $validatedData['isPublished'],
            'isFree' => $validatedData['isFree'],
            'course_id' => $validatedData['courseId'],
            'video_type' => 'mp4'
        ]);


        // Update the chapters array in the Course model
        $course = Course::findOrFail($validatedData['courseId']);
        $categories = Category::all();
        return redirect()->route('courses.edit', ['course' => $validatedData['courseId'], 'categories'=> $categories])
              ->with('success', 'Chapter Updated successfully');
        //return redirect()->route('courses.index')->with('success', 'Chapter updated successfully');
    }

    public function destroy(Chapter $chapter)
    {
        $chapter->delete();
        return redirect()->route('courses.index')->with('success', 'Chapter deleted successfully!');
    }
}
