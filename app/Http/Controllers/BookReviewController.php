<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\BookReview;
use App\Models\Book;
use App\Models\Author;
use App\Models\User;

class BookReviewController extends Controller
{
    function index()
    {
        $reviews = BookReview::all();
        
        foreach($reviews as $review)
        {
            $book = Book::find($review->book_id);
            $user = User::find($review->user_id);
            $authors = Author::where('book_id', $book->id)->get();

            $book->authors = $authors;
            $review->book = $book;
            $review->user = $user;
        }

        return view('community.bookReviews.index', ['reviews' => $reviews]);
    }

    function create()
    {
        $books = Book::where('user_id', Auth::user()->id)->get();

        return view('community.bookReviews.create', ['books' => $books]);
    }

    function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|integer',
            'review' => 'required'
        ]);

        $review = new BookReview();
        $review->user_id = Auth::user()->id;
        $review->book_id = $request->book_id;
        $review->review = strip_tags($request->review);
        $review->save();

        return redirect()->route('community.bookReview.index');
    }
}
