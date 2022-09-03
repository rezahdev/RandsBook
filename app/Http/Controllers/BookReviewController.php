<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\BookReview;
use App\Models\Book;
use App\Models\Author;
use App\Models\User;
use App\Models\BookReviewLike;
use App\Models\SavedBookReview;

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

    function like(Request $request)
    {
        if(empty($request->review_id) || !is_numeric($request->review_id))
        {
            return json_encode(['response' => 'FAILED', 'message' => 'Please refresh the page and try again.']);
        }

        $like = new BookReviewLike();
        $like->review_id = $request->review_id;
        $like->user_id = Auth::user()->id;
        $like->save();

        return json_encode(['response' => 'OK', 'message' => 'You liked this review.']);
    }

    function unlike(Request $request)
    {
        if(empty($request->review_id) || !is_numeric($request->review_id))
        {
            return json_encode(['response' => 'FAILED', 'message' => 'Please refresh the page and try again.']);
        }

        $like = BookReviewLike::where('review_id', $request->review_id)
                            ->where('user_id', Auth::user()->id)->first();
        $like->delete();

        return json_encode(['response' => 'OK', 'message' => 'You unliked this review.']);
    }

    function save(Request $request)
    {
        if(empty($request->review_id) || !is_numeric($request->review_id))
        {
            return json_encode(['response' => 'FAILED', 'message' => 'Please refresh the page and try again.']);
        }

        $review = new SavedBookReview();
        $review->review_id = $request->review_id;
        $review->user_id = Auth::user()->id;
        $review->save();

        return json_encode(['response' => 'OK', 'message' => 'You saved this review.']);
    }

    function unsave(Request $request)
    {
        if(empty($request->review_id) || !is_numeric($request->review_id))
        {
            return json_encode(['response' => 'FAILED', 'message' => 'Please refresh the page and try again.']);
        }

        $savedReview = SavedBookReview::where('review_id', $request->review_id)
                            ->where('user_id', Auth::user()->id)->first();
        $savedReview->delete();

        return json_encode(['response' => 'OK', 'message' => 'You unliked this review.']);
    }
}
