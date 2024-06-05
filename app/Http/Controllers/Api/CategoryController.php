<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\Book;
use App\Models\Course;

class CategoryController extends Controller
{
    public function allCategories()
    {
        // $categories = Category::all();
        $categories = Category::orderBy('created_at', 'desc')->get();
        return response()->json(['categories' => $categories]);
    }
        // Method to get all categories where type is 'Book'
        public function getBooks()
        {
            $categories = Category::where('type', 'Book')->get();
            return response()->json($categories);
        }
    
        // Method to get all categories where type is 'Course'
        public function getCourses()
        {
            $categories = Category::where('type', 'Course')->get();
            return response()->json($categories);
        }
    
         // Method to search categories by name and return books
         public function searchByName(Request $request)
         {
             $request->validate([
                 'category_id' => 'required|integer' // Assuming you're passing the category_id in the request
             ]);
         
             $categoryId = $request->input('category_id');
         
             // Find all books that belong to the specified category ID
             $books = Book::where('category_id', $categoryId)->get();
         
             return response()->json($books);
         }
         
       // Method to search categories by name and return courses
       public function searchCourseByName(Request $request)
       {
        $request->validate([
        'category_id' => 'required|integer' // Assuming you're passing the category_id in the request
        ]);

           $categoryId = $request->input('category_id');
   
           // Find all courses that belong to these categories
           $courses = Course::where('category_id', $categoryId)->get();
   
           return response()->json($courses);
       }
}