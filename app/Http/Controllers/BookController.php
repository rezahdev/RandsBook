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
        $response = Http::get('http://openlibrary.org/search.json?q=the+lord+of+the+rings');
        //$search_result = json_decode($response);
        return view('books.create', ['search_result' => $response]);
    }
}