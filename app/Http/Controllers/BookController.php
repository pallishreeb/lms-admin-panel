<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
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
            })->orderBy('created_at', 'desc')->paginate(10);

        return view('books.index', ['books' => $books]);
    }

    public function create()
    {
        $categories = Category::where('type', 'Book')->get();
        return view('books.create', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'cover_pic' => 'image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'language' => 'required',
            'price' => 'required|numeric',
            'attachmentUrl' => 'required|string',
            // 'pdf_book' => 'required|mimes:pdf|max:204800',
            'pages' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'is_published' => 'required|boolean',
            'is_free' => 'required|boolean',
            'status' => 'required',
        ]);

        $book = new Book($request->only([
            'title', 'description', 'language', 'price', 'pages', 'category_id', 'is_published', 'is_free','status'
        ]));

        // Upload cover picture
        // if ($request->hasFile('cover_pic')) {
        //     $coverPic = $request->file('cover_pic');
        //     $coverPicName = time() . '.' . $coverPic->getClientOriginalExtension();
        //     $coverPic->move(public_path('book_covers'), $coverPicName);
        //     $book->cover_pic = $coverPicName;
        // }
        $cover_picUrl = null;
        if ($request->hasFile('cover_pic')) {
            $cover_pic = $request->file('cover_pic');
            $cover_picPath = 'book_cover_pics/' . $cover_pic->getClientOriginalName();
            Storage::disk('s3')->put($cover_picPath, file_get_contents($cover_pic));
            $cover_picUrl = Storage::disk('s3')->url($cover_picPath);
            $book->cover_pic = $cover_picUrl;
        }

        // Upload PDF book
        // if ($request->hasFile('pdf_book')) {
        //     $pdfBook = $request->file('pdf_book');
        //     $pdfBookName = time() . '.' . $pdfBook->getClientOriginalExtension();
        //     $pdfBook->move(public_path('pdf_books'), $pdfBookName);
        //     $book->pdf_book = $pdfBookName;
        // }
        $attachmentUrl = null;
        // Check if the request has the videoUrl field with a string value
        if ($request->filled('attachmentUrl') && is_string($request->attachmentUrl)) {
            $attachmentUrl  = $request->attachmentUrl;
            $book->pdf_book = $attachmentUrl;
        }
        $book->save();

        return redirect()->route('books.index')->with('success', 'Book created successfully.');
    }

    public function edit(Book $book)
    {
        
        $categories = Category::where('type', 'Book')->get();
        return view('books.edit', ['book' => $book, 'categories' => $categories]);
    }

    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'cover_pic' => 'image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'language' => 'required',
            'price' => 'required|numeric',
            // 'pdf_book' => 'mimes:pdf|max:204800',  // Max 200MB
            'pages' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'is_published' => 'required|boolean',
            'is_free' => 'required|boolean',
            'status' => 'required',
        ]);

        $book->fill($request->only([
            'title', 'description', 'language', 'price', 'pages', 'category_id', 'is_published','is_free','status'
        ]));

        // Update cover picture
        // if ($request->hasFile('cover_pic')) {
        //     $coverPic = $request->file('cover_pic');
        //     $coverPicName = time() . '.' . $coverPic->getClientOriginalExtension();
        //     $coverPic->move(public_path('book_covers'), $coverPicName);
        //     $book->cover_pic = $coverPicName;
        // }
        $cover_picUrl = null;
        if ($request->hasFile('cover_pic')) {
            $cover_pic = $request->file('cover_pic');
            $cover_picPath = 'book_cover_pics/' . $cover_pic->getClientOriginalName();
            Storage::disk('s3')->put($cover_picPath, file_get_contents($cover_pic));
            $cover_picUrl = Storage::disk('s3')->url($cover_picPath);
            $book->cover_pic = $cover_picUrl;
        }

        // Update PDF book
        // if ($request->hasFile('pdf_book')) {
        //     $pdfBook = $request->file('pdf_book');
        //     $pdfBookName = time() . '.' . $pdfBook->getClientOriginalExtension();
        //     $pdfBook->move(public_path('pdf_books'), $pdfBookName);
        //     $book->pdf_book = $pdfBookName;
        // }
        $attachmentUrl = null;
        // Check if the request has the videoUrl field with a string value
        if ($request->filled('attachmentUrl') && is_string($request->attachmentUrl)) {
            $attachmentUrl  = $request->attachmentUrl;
            $book->pdf_book = $attachmentUrl;
        }
        
        $book->save();

        return redirect()->route('books.index')->with('success', 'Book updated successfully.');
    }

    public function destroy(Book $book)
    {
        try {
            // Check if the book is linked with other tables
            if ($book->videos()->exists()) {
                throw new \Exception('Course is linked with other tables and cannot be deleted.');
            }
    
            $book->delete();

            return redirect()->route('books.index')->with('success', 'Book deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('books.index')->with('error', $e->getMessage());
        }

    }
    public function upload(Request $request)
    {
        // Validate the request data, including file uploads
        $validatedData = $request->validate([
            'pdf_book' => 'required|mimes:pdf|max:512000',// Adjust max file size as needed
        ]);
       // Upload video file and get the URL
       $bookUrl = null;
       if ($request->hasFile('pdf_book')) {
           $pdf_book = $request->file('pdf_book');
           $pdf_bookPath = 'videos/' . $pdf_book->getClientOriginalName();
           Storage::disk('s3')->put($pdf_bookPath, file_get_contents($pdf_book));
           $bookUrl = Storage::disk('s3')->url($pdf_bookPath);
       }
 
       return response()->json(['success' => true, 'bookUrl' => $bookUrl]);
    }

}
