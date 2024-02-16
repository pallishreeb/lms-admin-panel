<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

use setasign\Fpdi\Fpdi;

class BookController extends Controller
{
    public function getBooks()
    {
        $books = Book::all();

        return response()->json(['books' => $books]);
    }
    public function getPdfBook($id)
    {
        $book = Book::findOrFail($id);

        return response()->json(['pdf_book' => $book->pdf_book]);
    }
    public function updatePdfBook(Request $request, $id)
    {

        $request->validate([
            'pdf_book' => 'required|mimes:pdf|max:20480',
        ]);

        $book = Book::findOrFail($id);
        //delete old pdf book if it exists  
        if ($book->pdf_book != null) {
            unlink(public_path('pdf_books/') . $book->pdf_book);
        }
        //save new file 
        // Update PDF book
        if ($request->hasFile('pdf_book')) {
            $pdfBook = $request->file('pdf_book');
            $pdfBookName = time() . '.' . $pdfBook->getClientOriginalExtension();
            $pdfBook->move(public_path('pdf_books'), $pdfBookName);
            $book->pdf_book = $pdfBookName;
            $book->save();

            return response()->json(['message' => 'PDF book updated successfully.']);
        } else {
            return response()->json(['error' => 'No PDF file provided.'], 400);
        }
    }

    public function fetchImage()
    {
        // URL of the image to fetch
        $imageUrl = 'https://www.iconpacks.net/icons/1/free-video-icon-818-thumb.png';

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

}
