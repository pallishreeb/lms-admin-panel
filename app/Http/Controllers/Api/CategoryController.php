<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;

class CategoryController extends Controller
{
    public function allCategories()
    {
        // $categories = Category::all();
        $categories = Category::orderBy('created_at', 'desc')->get();
        return response()->json(['categories' => $categories]);
    }
}