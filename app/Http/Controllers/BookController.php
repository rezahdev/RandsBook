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
        $book_list = Book::where('user_id', Auth::user()->id)
                        ->where('isWishlistItem', '0')
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

        $num_book_found = 'No book found';
        if(count($book_list) == 1)
        {
            $num_book_found = '1 book found';
        }
        else if(count($book_list) > 1)
        {
            $num_book_found = count($book_list) . ' books found';
        }
        return view('books.index', ['book_list' => $book_list, 'num_book_found' => $num_book_found]);
    }

    function show_from_model($id)
    {
        $book = Book::where([['id', strip_tags($id)], ['user_id', Auth::user()->id]])->first(); 

        if(is_null($book))
        {
            return view('books.show', ['response'=> 'No Book Found!', 'type' => 'NOT_FOUND']);
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
        $search_result = $this->search_by_isbn(strip_tags($isbn));

        if($search_result['response'] == "OK")
        {
            return view('books.show', ['book' => $search_result['book'], 'type' => 'SEARCH_DATA']);
        }
        else
        {
            return view('books.show', ['response' => $search_result['response'], 'type' => 'NOT_FOUND']);
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

    function create_with_data($isbn)
    {
        $search_result = $this->search_by_isbn(strip_tags($isbn));

        if($search_result['response'] == "OK")
        {
            return view('books.create', ['book' => $search_result['book']]);
        }
        else
        {
            return view('books.create', ['response' => $search_result['response']]);
        }
    }

    function store(Request $request)
    {
        $validator = $this->validateRequest($request);
        
        //If validator fails, returns the data back to create form with error messages
        if($validator->status == "FAILED")
        {          
            return view('books.create', ['book' => $this->requestToBookObject($request), 'errors' => $validator->errors]);
        }
        $this->saveBook($request);

        return redirect()->route('books.index');
    }   

    function edit($id)
    {
        $book = Book::where([['id', strip_tags($id)], ['user_id', Auth::user()->id]])->first(); 

        if(is_null($book))
        {
            return redirect()->route('books.index');
        }

        $authors = Author::where('book_id', $id)->get();
        $book->authors = $authors;

        $publishers = Publisher::where('book_id', $id)->get();
        $book->publishers = $publishers;

        $subjects = Subject::where('book_id', $id)->get();
        $book->subjects = $subjects;

        return view('books.edit', ['book'=> $book]);
    }

    function update(Request $request)
    {
        $validator = $this->validateRequest($request);
        
        //If validator fails, returns the data back to create form with error messages
        if($validator->status == "FAILED")
        {      
            $book = $this->requestToBookObject($request);
            $book->id = $request->id;
            return view('books.edit', ['book' => $book, 'errors' => $validator->errors]);
        }

        if(Book::select('id')->where([['id', strip_tags($request->id)], ['user_id', Auth::user()->id]])->exists())
        {   
            $this->saveBook($request, $request->id); 
        }
        return redirect()->route('books.index');
    }

    function delete($id)
    {
        $book = Book::where([['id', strip_tags($id)], ['user_id', Auth::user()->id]])->first();

        if(is_null($book))
        {
            return redirect()->route('books.index');
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

        $book->delete();

        return redirect()->route('books.index');
    }

    function wishlist()
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

        $num_book_found = 'No book found';
        if(count($book_list) == 1)
        {
            $num_book_found = '1 book found';
        }
        else if(count($book_list) > 1)
        {
            $num_book_found = count($book_list) . ' books found';
        }
        return view('books.wishlist', ['book_list' => $book_list, 'num_book_found' => $num_book_found]);
    }

    function search()
    {
        if(!isset($_GET['q']))
        {
            return view('books.search');
        }

        $q = strip_tags($_GET['q']);
        $response = Http::get('http://openlibrary.org/search.json?q=' . $q);
        $response = json_decode($response, false);

        $book_count = 0;
        $book_list = array();

        foreach($response->docs as $book)
        {
            if($book != null && property_exists($book, 'isbn') 
                && property_exists($book, 'title')
                && property_exists($book, 'author_name')
                && property_exists($book, 'publisher')
                && property_exists($book, 'number_of_pages_median'))
            {
                $book->isbn = $book->isbn[0];
                $book->total_pages = $book->number_of_pages_median;
                
                if(property_exists($book, 'cover_i'))
                {
                    $book->cover_url = 'https://covers.openlibrary.org/b/id/'. $book->cover_i .'-M.jpg';
                }
                else
                {
                    $book->cover_url = '/resources/RandsBookDefaultBookImg.png';
                }         

                array_push($book_list, $book);
                $book_count++;
            }
        }

        return view('books.search', ['book_list' => $book_list, 'book_count' => $book_count]);
    } 

    function search_by_isbn($isbn)
    {
        $response = Http::get('https://openlibrary.org/api/books?bibkeys=ISBN:' . strip_tags($isbn) . '&format=json&jscmd=data');  
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

    function update_read_pages(Request $request)
    {
        $request->validate(['book_id' => 'integer|required', 'read_pages' => 'required|integer']);
        $book = Book::where([['id', strip_tags($request->book_id)], ['user_id', Auth::user()->id]])->first(); ;

        if(is_null($book))
        {
            return "FAILED";
        }

        $book->read_pages = strip_tags($request->read_pages);
        $book->save();
        
        return "OK";
    }

    /**
     * If $id == null, the function is called to store info of a new book
     * else if $id value is passed, the function is called to update an existing book 
     */
    function saveBook($request, $id = null)
    {
        $book = ($id === null) ? new Book() : Book::where([['id', strip_tags($id)], ['user_id', Auth::user()->id]])->first();

        //save book info
        $book->user_id = Auth::user()->id;
        $book->book_id = strip_tags($request->isbn);
        $book->title = strip_tags($request->title);
        $book->subtitle = strip_tags($request->subtitle);
        $book->description = strip_tags($request->description);
        $book->publish_date = strip_tags($request->publish_date);

        $book->cover_url = $request->cover_url != "" ? $request->cover_url : '/resources/RandsBookDefaultBookImg.png';
        $book->total_pages = $request->total_pages != "" ? strip_tags($request->total_pages) : '0';
        $book->read_pages = $request->read_pages != "" ? strip_tags($request->read_pages) : '0';
        $book->comment = strip_tags($request->comment);
        $book->public_comment = strip_tags($request->public_comment);
        $book->save();

        //if updating book, first delete the existing authors of the book since we cannot update authors by author id
        if($id !== null)
        {
            $authors = Author::where('book_id', $book->id)->get();
            foreach($authors as $author)
            {
                $a = Author::find($author->id);
                $a->delete();
            }
        }

        //save author info
        for ($i = 1; $i <= 10; $i++) 
        {
            $a = 'author' . $i;
            if ($request->exists($a) && strlen($request->$a) > 0) 
            {
                $author = new Author();
                $author->book_id = $book->id;
                $author->name = $request->$a;
                $author->save();
            }
        }

        //if updating book, first delete the existing publishers of the book since we cannot update publishers by publisher id
        if($id !== null)
        {
            $publishers = Publisher::where('book_id', $book->id)->get();
            foreach($publishers as $publisher)
            {
                $p = Publisher::find($publisher->id);
                $p->delete();
            }
        }

        //save publisher info
        for ($i = 1; $i <= 4; $i++) 
        {
            $p = 'publisher' . $i;
            if ($request->exists($p) && strlen($request->$p) > 0) 
            {
                $publisher = new Publisher();
                $publisher->book_id = $book->id;
                $publisher->name = $request->$p;
                $publisher->save();
            }
        }

        //if updating book, first delete the existing subjects of the book since we cannot update subjects by subject id
        if($id !== null)
        {
            $subjects = Subject::where('book_id', $book->id)->get();
            foreach($subjects as $subject)
            {
                $s = Subject::find($subject->id);
                $s->delete();
            }
        }

        //save subjects info
        for ($i = 1; $i <= 3; $i++) 
        {
            $s = 'subject' . $i;
            if ($request->exists($s) && strlen($request->$s) > 0 && $s != 'subject0') 
            {
                $subject = new Subject();
                $subject->book_id = $book->id;
                $subject->name = $request->$s;
                $subject->save();
            }
        }

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

    function requestToBookObject($request)
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

        return $book;
    }
}