<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

class BookController extends Controller
{
    public function getBooks()
    {
        // $books = Book::all();
        $books = Book::orderBy('created_at', 'desc')->get();

        return response()->json(['books' => $books]);
    }
    public function getPdfBook($id)
    {
        $book = Book::findOrFail($id);

        return response()->json(['pdf_book' => $book->pdf_book]);
    }
    public function getBookDetails($id)
    {
        $book = Book::findOrFail($id);

        return response()->json(['pdf_book' => $book]);
    }
    public function updatePdfBook(Request $request, $id)
    {

        $request->validate([
            'pdf_book' => 'required|mimes:pdf|max:204800',
        ]);

        $book = Book::findOrFail($id);
        //delete old pdf book if it exists  
       
        // Update PDF book
        if ($request->hasFile('pdf_book')) {
            $pdfBook = $request->file('pdf_book');
            // Generate a unique file name using the book's title and current timestamp
           $timestamp = now()->timestamp;
           $pdfBookName = 'edited_pdf_books/' . $book->title . '_' . $timestamp . '_' . $pdfBook->getClientOriginalName();
            Storage::disk('s3')->put($pdfBookName, file_get_contents($pdfBook));
            $bookUrl = Storage::disk('s3')->url($pdfBookName);
            $book->pdf_book = $bookUrl;
            $book->save();

            return response()->json(['message' => 'PDF book updated successfully.']);
        } else {
            return response()->json(['error' => 'No PDF file provided.'], 400);
        }
    }

    public function fetchImage()
    {
        // URL of the image to fetch
        // $imageUrl = 'https://www.iconpacks.net/icons/1/free-video-icon-818-thumb.png';
        $imageUrl = 'https://png.pngtree.com/png-vector/20231115/ourmid/pngtree-play-icon-web-png-image_10604657.png';

        // Fetch the image data from the URL
        $imageData = file_get_contents($imageUrl);

        // Check if the image data was fetched successfully
        if ($imageData === false) {
            // Return an error response if the image couldn't be fetched
            return response()->json(['error' => 'Failed to fetch image'], 500);
        }

        // Return the image data as a response with the appropriate content type
        return response($imageData)->header('Content-Type', 'image/png');
    }

    public function search(Request $request)
    {
        // Get the search query from the request
        $query = $request->input('query');

        // Search for books
        $books = Book::where('title', 'like', "%$query%")->get();

        // Search for courses
        $courses = Course::where('title', 'like', "%$query%")->get();

        // Check if any results found
        if ($books->isEmpty() && $courses->isEmpty()) {
            return response()->json(['message' => 'No data found'], 404);
        }

        // Return the search results
        return response()->json(['books' => $books, 'courses' => $courses]);
    }

    
}
