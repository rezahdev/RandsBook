<?php 

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\Book;

class BookController extends Controller
{
    function index()
    {
        $books = Book::where('user_id', Auth::user()->id)->get();
        return view('books.index', ['books' => $books]);
    }

    function create()
    {
        return view('books.create');
    }

    function search()
    {
        $q = $_GET['q'];
        $response = Http::get('http://openlibrary.org/search.json?q=' . $q);
        $search_result = json_decode($response, true);

        $book_count = 0;
        $book_list = array();

        foreach($search_result['docs'] as $book)
        {
            if(array_key_exists('isbn', $book) 
                && array_key_exists('author_name', $book)
                && array_key_exists('publish_date', $book)
                && array_key_exists('publisher', $book))
            {
                $b = array();
                $b['title'] = $book['title'];
                $b['authors'] = $book['author_name'];
                $b['publish_date'] = $book['publish_date'];
                $b['publisher'] = $book['publisher'];
                $b['isbn'] = $book['isbn'][0];

                if(array_key_exists('number_of_pages_median', $book))
                {
                    $b['pages'] = $book['number_of_pages_median'];
                }
                else
                {
                    $b['pages'] = '0';
                }
                
                if(array_key_exists('cover_i', $book))
                {
                    $b['cover_url'] = 'https://covers.openlibrary.org/b/id/'. $book['cover_i'] .'-M.jpg';
                }
                else
                {
                    $b['cover_url'] = 'https://cdn.elearningindustry.com/wp-content/uploads/2016/05/top-10-books-every-college-student-read-1024x640.jpeg';
                }         

                array_push($book_list, $b);
                $book_count++;
            }
        }

        return view('books.create', [
            'book_list' => $book_list,
            'book_count' => $book_count]);
    }
}