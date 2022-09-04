<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Book;
use App\Models\Author;
use App\Models\Publisher;
use App\Models\Subject;
use App\Http\Controllers\SearchController;

class WishlistController extends Controller
{
    function index()
    {
        $book_list = Book::where('user_id', Auth::user()->id)
                        ->where('isWishlistItem', '1')
                        ->get();

        foreach($book_list as $book)
        {
            $author = Author::where('book_id', $book->id)->get();
            $publisher = Publisher::where('book_id', $book->id)->get();
            $subject = Subject::where('book_id', $book->id)->get();

            $book->authors = $author;
            $book->publishers = $publisher;
            $book->subjects = $subject;
        }

        $num_book_found = 'No book';
        if(count($book_list) == 1)
        {
            $num_book_found = '1 book';
        }
        else if(count($book_list) > 1)
        {
            $num_book_found = count($book_list) . ' books';
        }
        return view('books.wishlist', ['book_list' => $book_list, 'num_book_found' => $num_book_found]);
    }

    function store(Request $request)
    {   
        $book = Book::where('user_id', Auth::user()->id)
                    ->where('book_id', $request->edition_key)
                    ->first();

        if(!is_null($book))
        {
            //Book alredy exists either in library or wishlist
            return json_encode(['response' => 'FAILED', 'message' => 'This book is already in your library.']);
        }

        $search_controller = new SearchController();
        $search_result = $search_controller->search_by_edition_key($request->edition_key);
        
        if($search_result->response == 'OK')
        {            
            $data = $search_result->book; 

            $book = new Book();
            $book->book_id = $request->edition_key;
            $book->user_id = Auth::user()->id; 
            $book->title = $data->title;
            $book->subtitle = $data->subtitle;
            $book->total_pages = $data->total_pages;
            $book->description = $data->description;
            $book->cover_url = $data->cover_url;
            $book->isWishlistItem = '1';
            $book->save();

            //save author info
            foreach($data->authors as $a)
            {
                $author = new Author();
                $author->name = $a->name;
                $author->book_id = $book->id;
                $author->save();
            }

            //save publisher info
            foreach($data->publishers as $p)
            {
                $publisher = new Publisher();
                $publisher->name = $p;
                $publisher->book_id = $book->id;
                $publisher->save();
            }

            //save subjects info
            foreach($data->subjects as $index => $s)
            {
                $subject = new Subject();
                $subject->name = $s;
                $subject->book_id = $book->id;
                $subject->save();

                if($index == 2)
                {
                    break;
                }
            }

            return json_encode([
                'response' => $search_result->response, 
                'message' => $book->title . ' has been added to your wishlist.',
                'edition_key' => $book->id
            ]);
        }
        
        return json_encode(['response' => $search_result->response, 'message' => $search_result->message]);
    }

    function delete(Request $request)
    {
        $book = Book::where('user_id', Auth::user()->id)
                    ->where('id', $request->book_id)
                    ->where('isWishlistItem', '1')
                    ->first();
        
        if(is_null($book))
        {
            return json_encode(['response' => 'FAILED', 'message' => 'Invalid book id.']);
        }

        $authors = Author::where('book_id', $book->id)->get();           
        foreach($authors as $author)
        {
            $a = Author::find($author->id);
            $a->delete();

        }

        $publishers = Publisher::where('book_id', $book->id)->get();
        foreach($publishers as $publisher)
        {
            $p = Publisher::find($publisher->id);
            $p->delete();
        }

        $subjects = Subject::where('book_id', $book->id)->get();
        foreach($subjects as $subject)
        {
            $s = Subject::find($subject->id);
            $s->delete();
        }

        $title = $book->title;
        $edition_key = $book->book_id;
        $book->delete();
        
        return json_encode([
            'response' => 'OK', 
            'message' => $title . ' has been removed from your wishlist.',
            'edition_key' => $edition_key
         ]);
    }

    function wishlist_to_library(Request $request)
    {   
        $book_id = strip_tags($request->book_id);
        $book = Book::where([['id', $book_id], ['user_id', Auth::user()->id]])->first();  
        
        if(is_null($book))
        {
            return json_encode(['response' => 'FAILED', 'message' => 'This book was not found in your wishlist.']);
        }

        $book->isWishlistItem = 0;
        $book->save();

        return json_encode(['response' => 'OK', 'message' => $book->title . ' has been added to your library.']);
    }
}
