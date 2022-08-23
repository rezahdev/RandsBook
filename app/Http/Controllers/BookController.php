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
        $books = Book::where('user_id', Auth::user()->id)->get();
        $book_list = array();

        $books = json_decode($books, true);
        foreach($books as $book)
        {
            $author = Author::where('book_id', $book['id'])->get();
            $publisher = Publisher::where('book_id', $book['id'])->get();
            $subject = Subject::where('book_id', $book['id'])->get();

            $book['authors'] = $author;
            $book['publishers'] = $publisher;
            $book['subjects'] = $subject;
            array_push($book_list, $book);
        }
        return view('books.index', ['book_list' => $book_list]);
    }

    function create()
    {
        $book = array( 'title' => "",
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
                        'cover_url' => "");
        
        return view('books.create', ['book' => $book]);
    }

    function create_with_data()
    {
        $book = array( 'title' => "",
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
                            'cover_url' => "");

        if(isset($_POST['book_data']))
        {
            $book_data = json_decode($_POST['book_data'], true);
            if(array_key_exists('isbn', $book_data)) $book['isbn'] = $book_data['isbn']; 
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
            if(array_key_exists('cover_url', $book_data)) $book['cover_url'] = $book_data['cover_url'];
        }
        else
        {
            $book['title'] = "";
        }
        
        return view('books.create', ['book' => $book]);
    }

    function store(Request $request)
    {
        $valid = $this->validateRequest($request);
        if(!$valid['response'])
        {
            $book = array();
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

            return view('books.create', ['book' => $book, 'error' => $valid['message']]);
        }
        if(!Book::select('id')->where('book_id', $request->isbn)->exists())
        {       
                $book = new Book();
                $book->user_id = Auth::user()->id;
                $book->book_id = strip_tags($request->isbn);
                $book->title = strip_tags($request->title);
                $book->subtitle = strip_tags($request->subtitle);
                $book->description = strip_tags($request->description);
                $book->publish_date = strip_tags($request->publish_date);

                if($request->cover_url != "")
                    $book->cover_url = $request->cover_url;
                else
                    $book->cover_url = "https://i.pinimg.com/originals/a0/69/7a/a0697af2de64d67cf6dbb2a13dbc0457.png";

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

    function show_from_search_result($isbn)
    {
        $q = strip_tags($isbn);
        $response = Http::get('https://openlibrary.org/api/books?bibkeys=ISBN:' . $q . '&format=json&jscmd=data');
        //return view('books.show', ['response' => $response]);
        error_log($response);
        $response = json_decode($response, true);   
        $result = null;
        if(array_key_exists('ISBN:'.$q, $response)) $result = $response['ISBN:'.$q];   
        else return view('books.show', ['response' => 'No Book Found']); 

        
        $book = array();
                             
        $book['isbn'] = $isbn;
        
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

        if(array_key_exists('number_of_pages', $result))$book['total_pages'] = $result['number_of_pages'];
        else $book['total_pages'] = "0";
        
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

        return view('books.show', ['book' => $book, 'mode' => "ADD"]);
    }

    function show_from_model($id)
    {
        $book_l = Book::where('id', $id)->get(); 
        $book_l = json_decode($book_l, true);
        if(count($book_l) == 0)
        {
            return view('books.show', ['response'=> "No Book Found!"]);
        }

        $book_list = $book_l[0];

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

            return view('books.show', ['book' => $book, 'mode' => "EDIT"]);
        }
        else
        {
            return view('books.show', ['response'=> json_encode($book_list)]);
        }
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

        return $this->index();
    }

    function validateRequest($request)
    {
        if($request->title == null || strlen($request->title) < 1)
        {
            return array('response' => false, 'message' => 'Title cannot be empty.');
        }
        else if(strlen($request->total_pages) > 0)
        {
            if(!is_numeric($request->total_pages) || $request->total_pages < 0 || $request->total_pages > 10000)
            {
                return array('response' => false, 'message' => 'Number of pages must be between 0 and 10000.');
            }
        }
        else if(strlen($request->read_pages) > 0)
        {
            if(!is_numeric($request->read_pages) || $request->read_pages < 0 || $request->read_pages > 10000)
            {
                return array('response' => false, 'message' => 'Number of pages read must be between 0 and 10000.');
            }
        }
        else if($request->read_pages > $request->total_pages)
        {
            return array('response' => false, 'message' => 'Number of pages cannot exceed total number of pages.');
        }

        return array('response' => true, 'message' => 'Validation success.');
    }
}