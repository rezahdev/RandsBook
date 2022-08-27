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
        // $order = '';
        // if(isset($_GET['order']))
        // {
        //     $order
        // }
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

        $num_book_found = 'No book';
        if(count($book_list) == 1)
        {
            $num_book_found = '1 book';
        }
        else if(count($book_list) > 1)
        {
            $num_book_found = count($book_list) . ' books';
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

    function show_from_search_result($edition_key)
    {
        $search_result = $this->search_by_edition_key(strip_tags($edition_key));

        if($search_result->response == "OK")
        {
            return view('books.show', ['book' => $search_result->book, 'type' => 'SEARCH_DATA']);
        }
        else
        {
            return view('books.show', ['response' => $search_result->response, 'type' => 'NOT_FOUND']);
        }
    }

    function create()
    {
        $book = array(  'title' => "",
                        'edition_key' => "",
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

    function create_with_data($edition_key)
    {
        $search_result = $this->search_by_edition_key(strip_tags($edition_key));

        if($search_result->response == "OK")
        {
            return view('books.create', ['book' => $search_result->book]);
        }
        else
        {
            return view('books.create', ['response' => $search_result->response]);
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

        if(!Book::select('id')->where('user_id', Auth::user()->id)->where('book_id', $request->edition_key)->exists())
        {
            $this->saveBook($request);
            return redirect()->route('books.index');
        }
        else
        {
            return view('books.create', [
                'book' => $this->requestToBookObject($request), 
                'errors' => [(object)['message' => 'This book already exists in your library.']]
            ]);
        }
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

    function add_to_wishlist(Request $request)
    {
        try
        {
            $search_result = $this->search_by_edition_key(strip_tags($request->edition_key));
            
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
                return json_encode([
                    'response' => $search_result->response, 
                    'message' => $book->title . ' has been added to your wishlist.',
                    'book_id' => $book->id
                ]);
            }
            else
            {
                return json_encode(['response' => $search_result->response, 'message' => $search_result->message]);
            }
        }
        catch(\Exception $e)
        {
            return (object)['response' => 'FAILED', 'message' => $e->getMessage() /*'Unknonw error occurred. Please try again.'*/];
        }  
    }

    function remove_from_wishlist(Request $request)
    {
        $book = Book::where('user_id', Auth::user()->id)
                    ->where('id', $request->book_id)
                    ->where('isWishlistItem', '1')
                    ->first();
        
        if(is_null($book))
        {
            return json_encode(['response' => 'FAILED', 'message' => 'Invalid book id.']);
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
            $msg = 'Open Library API is currently not working. Please ty again or add book information manually.';
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
            return (object)['response' => 'FAILED', 'message' => 'Open Library API is currently inactive.'];
        }
        else
        {
            $response = json_decode($response, false);
            if(property_exists($response, 'error') && $response->error == 'notfound')
            {
                return (object)['response' => 'FAILED', 'message' => 'No book found.'];
            }
        } 

        $book = new \stdClass();
           
        $book->edition_key = $edition_key;
        $book->title = $response->title;
        $book->subtitle = property_exists($response, 'subtitle') ? $response->subtitle : '';
        $book->total_pages = property_exists($response, 'number_of_pages') ? $response->number_of_pages : '0';
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

        $author_info = Http::get('https://openlibrary.org' . $response->authors[0]->key . '.json'); 
        $book->authors = [json_decode($author_info, false)]; 

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

    /**
     * If $id == null, the function is called to store info of a new book
     * else if $id value is passed, the function is called to update an existing book 
     */
    function saveBook($request, $id = null)
    {
        $book = ($id === null) ? new Book() : Book::where([['id', strip_tags($id)], ['user_id', Auth::user()->id]])->first();

        //save book info
        $book->user_id = Auth::user()->id;
        $book->book_id = strip_tags($request->edition_key);
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

        $book->edition_key = $request->edition_key;
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