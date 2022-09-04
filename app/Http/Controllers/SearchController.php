<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\Book;

class SearchController extends Controller
{
    function search()
    {
        if(!isset($_GET['q']) || strlen($_GET['q']) < 1)
        {
            return view('books.search');
        }

        $q = strip_tags($_GET['q']);
        $response = Http::get('http://openlibrary.org/search.json?q=' . $q);

        if($response->failed())
        {
            $msg = 'Open Library API is currently inactive. Please ty again or add book information manually.';
            return view('books.search', ['api_connect_error' => (object)['message' => $msg]]);
        }
        $response = json_decode($response, false);

        $book_count = 0;
        $book_list = array();

        foreach($response->docs as $book)
        {
            if($book != null && property_exists($book, 'cover_edition_key') 
                && property_exists($book, 'title')
                && property_exists($book, 'author_name')
                && property_exists($book, 'publisher')
                && property_exists($book, 'number_of_pages_median'))
            {
                $book->edition_key = $book->cover_edition_key;

                $edition_data = Http::get('https://openlibrary.org/books/' . $book->edition_key . '.json');  
                $edition_data = json_decode($edition_data, false);

                //Edition key is later used to show a specific book from search result,
                //but some edition data does not contain the author field
                //So, filter search result to show only the results that have consistent edition info. 
                if(property_exists($edition_data, 'authors'))
                {
                    $book->total_pages = $book->number_of_pages_median;

                    if(property_exists($book, 'cover_i'))
                    {
                        $book->cover_url = 'https://covers.openlibrary.org/b/id/'. $book->cover_i .'-M.jpg';
                    }
                    else
                    {
                        $book->cover_url = '/resources/RandsBookDefaultBookImg.png';
                    }   

                    //To check if a book in the search result is in user's wishlist
                    $wishlistedBook = Book::where('user_id', Auth::user()->id)
                                            ->where('book_id', $book->edition_key)
                                            ->where('isWishlistItem', '1')
                                            ->first();
                    
                    if(!is_null($wishlistedBook))
                    {
                        $book->wishlistBookId = $wishlistedBook->id;
                        $book->isWishlisted = true;
                    }
                    else
                    {
                        $book->isWishlisted = false;
                    }

                    array_push($book_list, $book);
                    $book_count++;
                }     
            }
        }

        return view('books.search', ['book_list' => $book_list, 'book_count' => $book_count]);
    }

    function search_by_edition_key($edition_key)
    {
        $response = Http::get('https://openlibrary.org/books/' . $edition_key . '.json');  

        if($response->failed())
        {
            return (object)['response' => 'FAILED', 'message' => 'No book was found due to either invalid book id or irresponsive Open Library'];
        }
        $response = json_decode($response, false);

        $book = new \stdClass();
           
        $book->edition_key = $edition_key;
        $book->title = $response->title;
        $book->subtitle = property_exists($response, 'subtitle') ? $response->subtitle : '';
        $book->total_pages = property_exists($response, 'number_of_pages') ? $response->number_of_pages : '1';
        $book->description = property_exists($response, 'description') ? $response->description : '';
        $book->publish_date = property_exists($response, 'publish_date') ? $response->publish_date : '';
        $book->publishers = property_exists($response, 'publishers') ? $response->publishers : [];
        $book->subjects = property_exists($response, 'subjects') ? $response->subjects : [];

        if(property_exists($response, 'covers'))
        {
            $book->cover_url = 'https://covers.openlibrary.org/b/id/'. $response->covers[0] .'-L.jpg';
        }
        else
        {
           $book->cover_url = '/resources/RandsBookDefaultBookImg.png';
        }  

        $authors = [];
        foreach($response->authors as $index => $author)
        {
            $author_info = Http::get('https://openlibrary.org' . $author->key . '.json'); 
            $author_info = json_decode($author_info, false);
            array_push($authors, $author_info);

            if($index == 5) break;
        }
        
        $book->authors = $authors; 

        if(property_exists($response, 'description'))
        {
            if(is_object($response->description))
            {
                $book->description = $response->description->value;
            }
            else
            {
                $book->description = $response->description;
            }
        }
        else
        {
            $book->description = '';
        }
        
        $book->read_pages = "";
        $book->comment = "";
        $book->public_comment = "";

        return (object)['response' => 'OK', 'book' => $book];
    }
}
