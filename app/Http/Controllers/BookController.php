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
        $book = array( 'title' => "",
                            'subtitle' => "",
                            'authors' => array(),
                            'publishers' => array(),
                            'subjects' => array(),
                            'publish_date' => "",
                            'total_pages' => "",
                            'read_pages' => "",
                            'description' => "",
                            'cove_id' => "",
                            'comment' => "",
                            'public_comment' => "");


        if(isset($book_data))
        {
            if(array_key_exists('title', $book_data)) $book['title'] = $book_data['title']; 
            if(array_key_exists('subtitle', $book_data)) $book['subtitle'] = $book_data['subtitle'];
            if(array_key_exists('authors', $book_data)) $book['authors'] = $book_data['authors'];
            if(array_key_exists('publishers', $book_data)) $book['publishers'] = $book_data['publishers'];
            if(array_key_exists('publish_date', $book_data)) $book['publish_date'] = $book_data['publish_date'];
            if(array_key_exists('pages', $book_data)) $book['total_pages'] = $book_data['pages'];
            if(array_key_exists('read_pages', $book_data)) $book['read_pages'] = $book_data['read_pages'];
            if(array_key_exists('cover_id', $book_data)) $book['cover_id'] = $book_data['cover_id'];
            if(array_key_exists('comment', $book_data)) $book['comment'] = $book_data['comment'];
            if(array_key_exists('public_comment', $book_data)) $book['public_comment'] = $book_data['public_comment'];
            if(array_key_exists('description', $book_data)) $book['description'] = $book_data['description'];
        }
        else
        {
            $book['title'] = "No title";
        }
        
        return view('books.create', ['book' => $book]);
    }

    function create_with_data()
    {
        $book = array( 'title' => "",
                            'subtitle' => "",
                            'authors' => array(),
                            'publishers' => array(),
                            'subjects' => array(),
                            'publish_date' => "",
                            'total_pages' => "",
                            'read_pages' => "",
                            'description' => "",
                            'cove_id' => "",
                            'comment' => "",
                            'public_comment' => "");

        if(isset($_POST['book_data']))
        {
            $book_data = json_decode($_POST['book_data'], true);
            if(array_key_exists('title', $book_data)) $book['title'] = $book_data['title']; 
            if(array_key_exists('subtitle', $book_data)) $book['subtitle'] = $book_data['subtitle'];
            if(array_key_exists('authors', $book_data)) $book['authors'] = $book_data['authors'];
            if(array_key_exists('publishers', $book_data)) $book['publishers'] = $book_data['publishers'];
            if(array_key_exists('subjects', $book_data)) $book['subjects'] = $book_data['subjects'];
            if(array_key_exists('publish_date', $book_data)) $book['publish_date'] = $book_data['publish_date'];
            if(array_key_exists('pages', $book_data)) $book['total_pages'] = $book_data['pages'];
            if(array_key_exists('read_pages', $book_data)) $book['read_pages'] = $book_data['read_pages'];
            if(array_key_exists('cover_id', $book_data)) $book['cover_id'] = $book_data['cover_id'];
            if(array_key_exists('comment', $book_data)) $book['comment'] = $book_data['comment'];
            if(array_key_exists('public_comment', $book_data)) $book['public_comment'] = $book_data['public_comment'];
            if(array_key_exists('description', $book_data)) $book['description'] = $book_data['description'];
        }
        else
        {
            $book['title'] = $book_data['title'];
        }
        
        return view('books.create', ['book' => $book]);
    }

    function store()
    {

    }

    function search()
    {
        if(!isset($_GET['q']))
        {
            return view('books.search');
        }
        $q = strip_tags($_GET['q']);
        $response = Http::get('http://openlibrary.org/search.json?q=' . $q);
        $search_result = json_decode($response, true);

        $book_count = 0;
        $book_list = array();

        foreach($search_result['docs'] as $book)
        {
            if($book != null && array_key_exists('isbn', $book) 
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
                    $b['cover_url'] = 'https://i.pinimg.com/originals/a0/69/7a/a0697af2de64d67cf6dbb2a13dbc0457.png';
                }         

                array_push($book_list, $b);
                $book_count++;
            }
        }

        return view('books.search', [
            'book_list' => $book_list,
            'book_count' => $book_count]);
    }

    function show($id)
    {
        $q = strip_tags($id);
        $response = Http::get('https://openlibrary.org/api/books?bibkeys=ISBN:' . $q . '&format=json&jscmd=data');
        //return view('books.show', ['response' => $response]);
        error_log($response);
        $response = json_decode($response, true);   
        $result = null;
        if(array_key_exists('ISBN:'.$q, $response)) $result = $response['ISBN:'.$q];   
        else return view('books.show', ['response' => 'No Book Found']); 

        
        $book = array();
                             
        $book['isbn'] = $id;
        
        if(array_key_exists('cover', $result))
        {
            $book['cover_url'] = $result['cover']['large'];
        }
        else
        {
           $book['cover_url'] = 'https://i.pinimg.com/originals/a0/69/7a/a0697af2de64d67cf6dbb2a13dbc0457.png';
        }   
        $book['title'] = $result['title'];

        if(array_key_exists('authors', $result)) $book['authors'] = $result['authors'];
        else $book['authors'] = [0 => array('name' => 'Unknown', 'url' => "#")]; 

        $book['publishers'] = $result['publishers'];
        $book['publish_date'] = $result['publish_date'];

        if(array_key_exists('number_of_pages', $result))$book['pages'] = $result['number_of_pages'];
        else $book['pages'] = "Unknown";
        
        if(array_key_exists('subjects', $result)) $book['subjects'] = $result['subjects']; 
        else $book['subjects'] = [];  

        /*if(array_key_exists('description', $result['details'])) $book['description'] = $result['details']['description']['value']; 
        else {
            $book['work_id'] = $result['details']['works'][0]['key'];

            $response = Http::get('https://openlibrary.org' . $book['work_id']. '.json');
        
            error_log($response);
            $response = json_decode($response, true);
            if(array_key_exists('description', $response))
            {
                if(is_string($response['description']))$book['description'] = $response['description'];
                else $book['description'] = $response['description']['value'];
            }       
            else $book['description'] = "No Description found.";
        }*/

        return view('books.show', ['book' => $book]);
    }
}