<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        $courses = Course::with('category')
            ->when($query, function ($queryBuilder) use ($query) {
                return $queryBuilder->where('title', 'like', '%' . $query . '%')
                                    ->orWhere('description', 'like', '%' . $query . '%');
            })->when(!$query, function ($queryBuilder) {
                // If no query, return all categories
                return $queryBuilder;
            })->orderBy('created_at', 'desc')->paginate(10);

        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        $categories = Category::where('type', 'Course')->get();
        return view('courses.create',  ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'cover_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'price' => 'nullable|numeric',
            'isPublished' => 'boolean',
            'isFree' => 'boolean',
            'category_id' => 'required|exists:categories,id',
        ]);

        // Handle the creation of the course
        $course = new Course([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'isPublished' => $request->input('isPublished', false),
            'isFree' => $request->input('isFree', false),
            'category_id' => $request->input('category_id'),
        ]);

        // Handle file upload for cover_pic
        // if ($request->hasFile('cover_pic')) {
        //     $coverPic = $request->file('cover_pic');
        //     $coverPicName = time() . '.' . $coverPic->getClientOriginalExtension();
        //     $coverPic->move(public_path('course_covers'), $coverPicName);
        //     $course->cover_pic = $coverPicName;
        // }
        $cover_picUrl = null;
        if ($request->hasFile('cover_pic')) {
            $cover_pic = $request->file('cover_pic');
            $cover_picPath = 'course_cover_pics/' . $cover_pic->getClientOriginalName();
            Storage::disk('s3')->put($cover_picPath, file_get_contents($cover_pic));
            $cover_picUrl = Storage::disk('s3')->url($cover_picPath);
            $course->cover_pic = $cover_picUrl;
        }

        $course->save();
        return redirect()->route('courses.edit', ['course' => $course])->with('success', 'Course created successfully!');
        //return redirect()->route('courses.index')->with('success', 'Course created successfully!');
    }

    public function edit(Course $course)
    {
        // You may need to load categories here if needed
        $categories = Category::where('type', 'Course')->get();
        return view('courses.edit', compact('course', 'categories'));
        //return view('courses.edit', compact('course'));
    }
    public function update(Request $request, Course $course)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|max:255',
            'description' => 'required',
            'isPublished' => 'required|boolean',
            'isFree' => 'required|boolean',
            'cover_pic' => 'image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'price' => 'required|numeric',
            // Add other validation rules as needed
        ]);

        // Use the update method with only the provided attributes
        $course->fill($request->only([
            'category_id', 'title', 'description', 'isPublished','isFree','price',
        ]));

        // Update cover picture if provided
        // if ($request->hasFile('cover_pic')) {
        //     $coverPicPath = $request->file('cover_pic')->store('course_covers', 'public');
        //     $course->cover_pic = $coverPicPath;
        // }
        $cover_picUrl = null;
        if ($request->hasFile('cover_pic')) {
            $cover_pic = $request->file('cover_pic');
            $cover_picPath = 'course_cover_pics/' . $cover_pic->getClientOriginalName();
            Storage::disk('s3')->put($cover_picPath, file_get_contents($cover_pic));
            $cover_picUrl = Storage::disk('s3')->url($cover_picPath);
            $course->cover_pic = $cover_picUrl;
        }

        $course->save(); 
        return redirect()->route('courses.edit', ['course' => $course])->with('success', 'Course updated successfully!');
    }
    public function destroy(Course $course)
    {
        try {
            // Check if the course is linked with other tables
            if ($course->chapters()->exists()) {
                throw new \Exception('Course is linked with other tables and cannot be deleted.');
            }
    
            $course->delete();
            return redirect()->route('courses.index')->with('success', 'Course deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('courses.index')->with('error', $e->getMessage());
        }

    }

    public function uploadImage(Request $request)
    {
        // Validate the request data, including file uploads
        $validatedData = $request->validate([
            'cover_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',// Adjust max file size as needed
        ]);
       // Upload video file and get the URL
       $cover_picUrl = null;
       if ($request->hasFile('cover_pic')) {
           $cover_pic = $request->file('cover_pic');
           $cover_picPath = 'course_cover_pics/' . $cover_pic->getClientOriginalName();
           Storage::disk('s3')->put($cover_picPath, file_get_contents($cover_pic));
           $cover_picUrl = Storage::disk('s3')->url($cover_picPath);
       }
 
       return response()->json(['success' => true, 'cover_picUrl' => $cover_picUrl]);
    }
}

