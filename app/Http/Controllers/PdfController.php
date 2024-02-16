<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class PdfController extends Controller
{
    public function showPDF($id)
    {
        $book = Book::findOrFail($id);

        // Pass book data to the view
        return view('books.edit-pdf', compact('book'));
    }
}
