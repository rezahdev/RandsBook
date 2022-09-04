<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $reviews = null;
        
        if(isset($_GET['sort']))
        {
            $sort = DB::connection()->getPDO()->quote($_GET['sort']);

            if($sort == 'date')
            {
                $reviews = BookReview::orderBy('updated_at', 'DESC')->get();
            }
            else if($sort == 'like')
            {
                $query = "SELECT * FROM book_reviews INNER JOIN book_review_likes on book_reviews.id = book_review_likes.review_id ";
                $query .= "ORDER BY COUNT(book_review_likes.id) DESC";
                $reviews = BookReview::select($query)->get();
            }
            else if($sort == 'saved')
            {
                $query = "SELECT * FROM book_reviews INNER JOIN saved_book_reviews on book_reviews.id = saved_book_reviews.review_id ";
                $query .= "ORDER BY COUNT(saved_book_reviews.id) DESC";
                $reviews = BookReview::select($query)->get();
            }
            else
            {
                $reviews = BookReview::orderBy('updated_at')->get();
            }
        }
        else
        {
            $reviews = BookReview::orderBy('updated_at')->get();
        }
        
        foreach($reviews as $review)
        {
            $review->reviewPreview = $review->review;
            $review->isLongReview = false;

            if(strlen($review->review) > 150)
            {
                $review->isLongReview = true;
                $review->reviewPreview = substr($review->review, 0, 150) . '...';
            }

            $book = Book::find($review->book_id);
            $authors = Author::where('book_id', $book->id)->get();
            $book->authors = $authors;
            $review->book = $book;

            $user = User::find($review->user_id);
            $review->user = $user;
            $review->isReviewdByThisUser = ( $review->user_id == Auth::user()->id );

            $reviewLikes = BookReviewLike::where('review_id', $review->id)->get();
            $review->likeCount = count($reviewLikes);
            $review->isLikedByThisUser = BookReviewLike::select('id')
                                                          ->where('review_id', $review->id)
                                                          ->where('user_id', Auth::user()->id)
                                                          ->exists();
            
            $review->isSavedByThisUser = SavedBookReview::select('id')
                                                          ->where('review_id', $review->id)
                                                          ->where('user_id', Auth::user()->id)
                                                          ->exists();
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

    function edit($id)
    {
        $review = BookReview::where('id', $id)->where('user_id', Auth::user()->id)->first();

        if(is_null($review))
        {
            return view('community.bookReviews.edit', ['response' => 'No Review Found!']);
        }

        $book = Book::find($review->book_id);
        return view('community.bookReviews.edit', ['review' =>$review, 'book' => $book]);
    }

    function update(Request $request)
    {
        $request->validate([
            'book_id' => 'required|integer',
            'review' => 'required'
        ]);

        $review = BookReview::find($request->review_id);
        $review->review = strip_tags($request->review);
        $review->save();

        return redirect()->route('community.bookReview.index');
    }

    function delete(Request $request)
    {
        if(empty($request->review_id) || !is_numeric($request->review_id))
        {
            return json_encode(['response' => 'FAILED', 'message' => 'Please refresh the page and try again.']);
        }

        $review = BookReview::find($request->review_id);
        $review->delete();

        return json_encode(['response' => 'OK', 'message' => 'Review was successfully deleted.']);
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
