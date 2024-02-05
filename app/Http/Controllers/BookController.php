<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        $books = Book::with('category')
            ->when($query, function ($queryBuilder) use ($query) {
                return $queryBuilder->where('title', 'like', '%' . $query . '%')
                                    ->orWhere('description', 'like', '%' . $query . '%');
            })->when(!$query, function ($queryBuilder) {
                // If no query, return all categories
                return $queryBuilder;
            })->paginate(10);

        return view('books.index', ['books' => $books]);
    }

    public function create()
    {
        $categories = Category::all();
        return view('books.create', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'cover_pic' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'language' => 'required',
            'price' => 'required|numeric',
            'pdf_book' => 'required|mimes:pdf|max:20480',
            'pages' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'is_published' => 'required|boolean',
            'is_free' => 'required|boolean',
        ]);

        $book = new Book($request->only([
            'title', 'description', 'language', 'price', 'pages', 'category_id', 'is_published', 'is_free'
        ]));

        // Upload cover picture
        if ($request->hasFile('cover_pic')) {
            $coverPic = $request->file('cover_pic');
            $coverPicName = time() . '.' . $coverPic->getClientOriginalExtension();
            $coverPic->move(public_path('book_covers'), $coverPicName);
            $book->cover_pic = $coverPicName;
        }

        // Upload PDF book
        if ($request->hasFile('pdf_book')) {
            $pdfBook = $request->file('pdf_book');
            $pdfBookName = time() . '.' . $pdfBook->getClientOriginalExtension();
            $pdfBook->move(public_path('pdf_books'), $pdfBookName);
            $book->pdf_book = $pdfBookName;
        }

        $book->save();

        return redirect()->route('books.index')->with('success', 'Book created successfully.');
    }

    public function edit(Book $book)
    {
        
        $categories = Category::all();
        return view('books.edit', ['book' => $book, 'categories' => $categories]);
    }

    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'cover_pic' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'language' => 'required',
            'price' => 'required|numeric',
            'pdf_book' => 'mimes:pdf|max:20480',
            'pages' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'is_published' => 'required|boolean',
            'is_free' => 'required|boolean',
        ]);

        $book->fill($request->only([
            'title', 'description', 'language', 'price', 'pages', 'category_id', 'is_published','is_free'
        ]));

        // Update cover picture
        if ($request->hasFile('cover_pic')) {
            $coverPic = $request->file('cover_pic');
            $coverPicName = time() . '.' . $coverPic->getClientOriginalExtension();
            $coverPic->move(public_path('book_covers'), $coverPicName);
            $book->cover_pic = $coverPicName;
        }

        // Update PDF book
        if ($request->hasFile('pdf_book')) {
            $pdfBook = $request->file('pdf_book');
            $pdfBookName = time() . '.' . $pdfBook->getClientOriginalExtension();
            $pdfBook->move(public_path('pdf_books'), $pdfBookName);
            $book->pdf_book = $pdfBookName;
        }

        $book->save();

        return redirect()->route('books.index')->with('success', 'Book updated successfully.');
    }

    public function destroy(Book $book)
    {
        $book->delete();

        return redirect()->route('books.index')->with('success', 'Book deleted successfully.');
    }
}
