<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function getCourses()
    {
        $courses = Course::with('category', 'chapters')->orderBy('created_at', 'desc')->get();
    
        return response()->json(['courses' => $courses]);
    }
    
    public function getCourseChapters(Request $request, $id)
    {
        $course = Course::with('chapters')->findOrFail($id);
    
        return response()->json(['course' => $course]);
    }
    
}
