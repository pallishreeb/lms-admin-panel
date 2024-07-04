<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        
        $categories = Category::when($query, function ($queryBuilder) use ($query) {
            return $queryBuilder->where('name', 'like', '%' . $query . '%');
        })->when(!$query, function ($queryBuilder) {
            // If no query, return all categories
            return $queryBuilder;
        })->orderBy('created_at', 'desc')->paginate(10);
    
        return view('categories.index', ['categories' => $categories]);
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'type' => 'required|in:Book,Course',
            'price' => 'nullable|numeric', 
        ]);

        // Check for unique combination of name and type
        $existingCategory = Category::where('name', $validatedData['name'])
                                    ->where('type', $validatedData['type'])
                                    ->first();

        if ($existingCategory) {
            return redirect()->back()->withErrors(['name' => 'The combination of name and type must be unique.']);
        }

        // $category = new Category;
        // $category->name = $request->name;
        // $category->type = $request->type;


        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName ='category/' . time() . '.' . $image->getClientOriginalExtension();
            Storage::disk('s3')->put($imageName, file_get_contents($image));
            $categoryImage = Storage::disk('s3')->url($imageName);
            // $category->image = $categoryImage;
            $validatedData['image'] = $categoryImage;
        }


        // $category->save();
        Category::create($validatedData);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        return view('categories.edit', ['category' => $category]);
    }

    public function update(Request $request, Category $category)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'type' => 'required|in:Book,Course',
            'price' => 'nullable|numeric', 
        ]);

        // Check for unique combination of name and type, excluding the current category
        $existingCategory = Category::where('name', $validatedData['name'])
                                    ->where('type', $validatedData['type'])
                                    ->where('id', '!=', $category->id)
                                    ->first();

        if ($existingCategory) {
            return redirect()->back()->withErrors(['name' => 'The combination of name and type must be unique.']);
        }

        // $category->name = $request->name;
        // $category->type = $request->type;
         
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName ='category/' . time() . '.' . $image->getClientOriginalExtension();
            Storage::disk('s3')->put($imageName, file_get_contents($image));
            $categoryImage = Storage::disk('s3')->url($imageName);
            // $category->image = $categoryImage;
            $validatedData['image'] = $categoryImage;
        }

        // $category->save();
        $category->update($validatedData);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function deleteConfirmation(Category $category)
    {
        return view('categories.delete-confirmation', [
            'item' => $category,
            'type' => 'category',
            'route' => route('categories.destroy', $category->id),
            'backRoute' => route('categories.index'),
        ]);
    }
    
    public function destroy(Category $category)
    {
        try {
            // Check if the category is linked with other tables
            if ($category->books()->exists() || $category->courses()->exists()) {
                throw new \Exception('Category is linked with other tables and cannot be deleted.');
            }
    
            $category->delete();
    
            return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('categories.index')->with('error', $e->getMessage());
        }
    }
}
