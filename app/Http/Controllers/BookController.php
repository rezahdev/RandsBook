<?php 

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Author;
use App\Models\Publisher;
use App\Models\Subject;

class BookController extends Controller
{
    function index()
    {
        $book_list = Book::where('user_id', Auth::user()->id)->get();

        foreach($book_list as $book)
        {
            $author = Author::where('book_id', $book->id)->get();
            $publisher = Publisher::where('book_id', $book->id)->get();
            $subject = Subject::where('book_id', $book->id)->get();

            $book->authors = $author;
            $book->publishers = $publisher;
            $book->subjects = $subject;
        }
        return view('books.index', ['book_list' => $book_list]);
    }

    function show_from_model($id)
    {
        $book = Book::find($id); 

        if(is_null($book))
        {
            return view('books.show', ['response'=> "No Book Found!"]);
        }

        $authors = Author::where('book_id', $id)->get();
        $book->authors = $authors;

        $publishers = Publisher::where('book_id', $id)->get();
        $book->publishers = $publishers;

        $subjects = Subject::where('book_id', $id)->get();
        $book->subjects = $subjects;

        return view('books.show', ['book' => $book, 'type' => 'MODEL_DATA']);
    }

    function show_from_search_result($isbn)
    {
        $isbn = strip_tags($isbn);
        $search_result = $this->search_by_isbn($isbn);

        if($search_result['response'] == "OK")
        {
            return view('books.show', ['book' => $search_result['book'], 'type' => 'SEARCH_DATA']);
        }
        else
        {
            return view('books.show', ['response' => $search_result['response'], 'type' => 'NOT_FOUND']);
        }
    }

    function create_with_data($isbn)
    {
        $isbn = strip_tags($isbn);
        $search_result = $this->search_by_isbn($isbn);

        if($search_result['response'] == "OK")
        {
            return view('books.create', ['book' => $search_result['book']]);
        }
        else
        {
            return view('books.create', ['response' => $search_result['response']]);
        }
    }

    function create()
    {
        $book = array(  'title' => "",
                        'isbn' => "",
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
                        'public_comment' => "",
                        'cover_url' => ""
                    );
        
        return view('books.create', ['book' => (object)$book]);
    }

    function store(Request $request)
    {
        $validator = $this->validateRequest($request);
        
        //If validator fails, returns the data back to create form with error messages
        if($validator->status == "FAILED")
        {
            $book = new \stdClass();

            $book->isbn = $request->isbn;
            $book->cover_url = $request->cover_url;
            $book->title = $request->title;
            $book->subtitle = $request->subtitle;
            $book->description = $request->description;
            $book->total_pages = $request->total_pages;
            $book->read_pages = $request->read_pages;
            $book->comment = $request->comment;
            $book->public_comment = $request->public_comment;
            $book->publish_date = $request->publish_date;

            $authors = array();
            for($i=1; $i<=10; $i++)
            {
                $a = 'author' . $i;
                if($request->exists($a))
                {
                    array_push($authors, (object)['name' => $request->$a]);
                }
            }
            $book->authors = $authors;

            $publishers = array();
            for($i=1; $i<=4; $i++)
            {
                $p = 'publisher' . $i;

                if($request->exists($p))
                {
                    array_push($publishers, (object)['name' => $request->$p]);
                }
            }
            $book->publishers = $publishers;

            $subjects = array();
            for($i=1; $i<=3; $i++)
            {
                $s = 'subject' . $i;

                if($request->exists($s) && $s != 'subject0')
                {
                    array_push($subjects, (object)['name' => $request->$s]);
                }
            }
            $book->subjects = $subjects;

            return view('books.create', ['book' => $book, 'errors' => $validator->errors]);
        }
        
        //save book info
        $book = new Book();
        $book->user_id = Auth::user()->id;
        $book->book_id = strip_tags($request->isbn);
        $book->title = strip_tags($request->title);
        $book->subtitle = strip_tags($request->subtitle);
        $book->description = strip_tags($request->description);
        $book->publish_date = strip_tags($request->publish_date);

        if($request->cover_url != "")
        {
            $book->cover_url = $request->cover_url;
        }                 
        else
        {
            $book->cover_url = '/resources/RandsBookDefaultBookImg.png';

        }
        
        $book->total_pages = $request->total_pages != "" ? strip_tags($request->total_pages) : '0';
        $book->read_pages = $request->read_pages != "" ? strip_tags($request->read_pages) : '0';                          
        $book->comment = strip_tags($request->comment);
        $book->public_comment = strip_tags($request->public_comment);
        $book->save();

        //save author info
        for($i=1; $i<=10; $i++)
        {
            $a = 'author' . $i;
            if($request->exists($a) && strlen($request->$a) > 0)
            {
                $author = new Author();
                $author->book_id = $book->id;
                $author->name = $request->$a;
                $author->save();
            }
        }

        //save publisher info
        for($i=1; $i<=4; $i++)
        {
            $p = 'publisher' . $i;
            if($request->exists($p) && strlen($request->$p) > 0)
            {
                $publisher = new Publisher();
                $publisher->book_id = $book->id;
                $publisher->name = $request->$p;
                $publisher->save();
            }
        }

        //save subjects info
        for($i=1; $i<=3; $i++)
        {
            $s = 'subject' . $i;
            if($request->exists($s) && strlen($request->$s) > 0 && $s != 'subject0')
            {
                $subject = new Subject();
                $subject->book_id = $book->id;
                $subject->name = $request->$s;
                $subject->save();
            }
        }
        return redirect()->route('books.index');
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

    

    function edit($id)
    {
        $book_l = Book::where('id', $id)->get(); 

        $book_list = json_decode($book_l, true)[0];

        if(count($book_list) > 0)
        {
            $book = array();

            $book['id'] = $id;
            $book['isbn'] = $book_list['book_id'];
            $book['title'] = $book_list['title'];
            $book['subtitle'] = $book_list['subtitle'];
            $book['description'] = $book_list['description'];
            $book['total_pages'] = $book_list['total_pages'];
            $book['read_pages'] = $book_list['read_pages'];
            $book['publish_date'] = $book_list['publish_date'];
            $book['cover_url'] = $book_list['cover_url'];
            $book['comment'] = $book_list['comment'];
            $book['public_comment'] = $book_list['public_comment'];

            $authors = Author::where('book_id', $id)->get();
            $book['authors'] = $authors;

            $publishers = Publisher::where('book_id', $id)->get();
            $book['publishers'] = $publishers;

            $subjects = Subject::where('book_id', $id)->get();
            $book['subjects'] = $subjects;

            return view('books.edit', ['book' => $book]);
        }
        else
        {
            return view('books.show', ['response'=> json_encode($book_list)]);
        }
    }

    function update(Request $request)
    {
        $valid = $this->validateRequest($request);
        if(!$valid['response'])
        {
            $book = array();
            $book['id'] = $request->id;
            $book['isbn'] = $request->isbn;
            $book['cover_url'] = $request->cover_url;
            $book['title'] = $request->title;
            $book['subtitle'] = $request->subtitle;
            $book['description'] = $request->description;
            $book['total_pages'] = $request->total_pages;
            $book['read_pages'] = $request->read_pages;
            $book['comment'] = $request->comment;
            $book['public_comment'] = $request->public_comment;
            $book['publish_date'] = $request->publish_date;
            $authors = array();

            for($i=1; $i<=10; $i++){
                $a = 'author' . $i;
                if($request->exists($a))
                {
                    array_push($authors, ['name' => $request->$a]);
                }
            }
            $book['authors'] = $authors;

            $publishers = array();
            for($i=1; $i<=4; $i++)
                {
                    $p = 'publisher' . $i;

                    if($request->exists($p))
                    {
                        array_push($publishers, ['name' => $request->$p]);
                    }
                }

            $book['publishers'] = $publishers;

                $subjects = array();
                for($i=1; $i<=3; $i++)
                {
                    $s = 'subject' . $i;

                    if($request->exists($s) && $s != 'subject0')
                    {
                        array_push($subjects, ['name' => $request->$s]);
                    }
                }
            $book['subjects'] = $subjects;

            return view('books.edit', ['book' => $book, 'error' => $valid['message']]);
        }
        if(Book::select('id')->where('id', $request->id)->exists())
        {       
                $book = Book::find($request->id);
                $book->book_id = strip_tags($request->isbn);
                $book->title = strip_tags($request->title);
                $book->subtitle = strip_tags($request->subtitle);
                $book->description = strip_tags($request->description);
                $book->publish_date = strip_tags($request->publish_date);

                if($request->total_pages != "" && $request->total_pages != null)
                    $book->total_pages = strip_tags($request->total_pages);
                else 
                    $book->total_pages = 0;

                if($request->read_pages != "" && $request->read_pages != null)
                {
                    $book->read_pages = strip_tags($request->read_pages);
                }
                else 
                    $book->read_pages = "0";
                
                $book->comment = strip_tags($request->comment);
                $book->public_comment = strip_tags($request->public_comment);
                $book->save();

                $authors = Author::where('book_id', $book->id)->get();
                //$authors = json_decode($authors, true);
                foreach($authors as $author)
                {
                    $a = Author::find($author->id);
                    $a->delete();

                }
                for($i=1; $i<=10; $i++){
                    $a = 'author' . $i;
                    if($request->exists($a) && strlen($request->$a) > 0)
                    {
                        $author = new Author();
                        $author->book_id = $book->id;
                        $author->name = $request->$a;
                        $author->save();
                    }
                }

                $publishers = Publisher::where('book_id', $book->id)->get();
                //$publishers = json_decode($publishers, true);
                foreach($publishers as $publisher)
                {
                    $p = Publisher::find($publisher->id);
                    $p->delete();
                }

                for($i=1; $i<=4; $i++)
                {
                    $p = 'publisher' . $i;
        
                    if($request->exists($p) && strlen($request->$p) > 0)
                    {
                        $publisher = new Publisher();
                        $publisher->book_id = $book->id;
                        $publisher->name = $request->$p;
                        $publisher->save();
                    }
                }

                $subjects = Subject::where('book_id', $book->id)->get();
                //$subjects = json_decode($subjects, true);
                foreach($subjects as $subject)
                {
                    $s = Subject::find($subject->id);
                    $s->delete();
                }

                for($i=1; $i<=3; $i++)
                {
                    $s = 'subject' . $i;

                    if($request->exists($s) && strlen($request->$s) > 0 && $s != 'subject0')
                    {
                        $subject = new Subject();
                        $subject->book_id = $book->id;
                        $subject->name = $request->$s;
                        $subject->save();
                    }
                }
  
        }
        return $this->index();
    }

    function delete($id)
    {
        $book = Book::find($id);
        
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

        $book->delete();

        return redirect()->route('books.index');
    }

    function search_by_isbn($isbn)
    {
        $response = Http::get('https://openlibrary.org/api/books?bibkeys=ISBN:' . $isbn . '&format=json&jscmd=data');  
        $response = json_decode($response, false); 
        
        $isbn_key = 'ISBN:' . $isbn;
        if(!property_exists($response, $isbn_key))
        {
            return ['response' => 'NO_DATA'];      
        }   
        
        $response = $response->$isbn_key;
        $book = new \stdClass();
                             
        $book->isbn = $isbn;
        $book->title = $response->title;

        $book->subtitle = property_exists($response, 'subtitle') ? $response->subtitle : "";
        $book->total_pages = property_exists($response, 'number_of_pages') ? $response->number_of_pages : "";
        $book->publish_date = property_exists($response, 'publish_date') ? $response->publish_date : "";
        $book->authors = property_exists($response, 'authors') ? $response->authors : [];
        $book->publishers = property_exists($response, 'publishers') ? $response->publishers : [];
        $book->subjects = property_exists($response, 'subjects') ? $response->subjects : [];

        if(property_exists($response, 'cover'))
        {
            $book->cover_url = $response->cover->large;
        }
        else
        {
           $book->cover_url = '/resources/RandsBookDefaultBookImg.png';
        }  

        $book->read_pages = "";
        $book->description = "";
        $book->comment = "";
        $book->public_comment = "";

        return ['response' => 'OK', 'book' => $book];
    }

    function validateRequest($request)
    {
        $errors = array();
        $isValidRequest = true;

        if($request->title == null || strlen($request->title) < 1)
        {
            $isValidRequest = false;
            array_push($errors, (object)['message' => 'Title cannot be empty.']);
        }
        
        if(strlen($request->total_pages) > 0)
        {
            if(!is_numeric($request->total_pages) || $request->total_pages < 0 || $request->total_pages > 10000)
            {
                $isValidRequest = false;
                array_push($errors, (object)['message' => 'Number of pages must be an integer between 0 and 10000.']);
            }
        }
        
        if(strlen($request->read_pages) > 0)
        {
            if(!is_numeric($request->read_pages) || $request->read_pages < 0 || $request->read_pages > 10000)
            {
                $isValidRequest = false;
                array_push($errors, (object)['message' => 'Number of pages read must be an integer between 0 and 10000.']);
            }
        }
        
        if($request->read_pages > $request->total_pages)
        {
            $isValidRequest = false;
            array_push($errors, (object)['message' => 'Number of pages cannot exceed total number of pages.']);
        }

        if($isValidRequest)
        {
            return (object)['status' => 'OK', 'errors' => []];
        }
        else
        {
            return (object)['status' => 'FAILED', 'errors' => $errors];
        }
    }
}