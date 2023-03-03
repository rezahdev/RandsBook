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
use App\Http\Controllers\SearchController;

class BookController extends Controller
{
    function index()
    {
        $filter = null;
        $query = "SELECT * FROM books where user_id = '" . Auth::user()->id . "' AND isWishlistItem = '0'";
    
        if(isset($_GET['filter']))
        {
            $filter = $_GET['filter'];
            $query ="SELECT * FROM books b1 INNER JOIN books b2 on b1.id = b2.id WHERE b1.user_id='" . Auth::user()->id . "' ";
            if($filter == 'completed')
            {            
                $query .= "AND b1.isWishlistItem = '0' AND b1.total_pages = b2.read_pages";
            }
            else if($filter == 'progress')
            {
                $query .= "AND b1.isWishlistItem = '0' AND b1.total_pages <> b2.read_pages";
            }
        }

        if(isset($_GET['sort']) && isset($_GET['order']))
        {
            $sort = $_GET['sort'];
            $order = $_GET['order'];

            if(($filter == 'completed' || $filter == 'progress'))
            {
                if($sort == 'date_added' && $order == 'asc')
                {            
                    $query .= " ORDER BY b1.created_at";
                }
                else if($sort == 'date_added' && $order == 'desc')
                {            
                    $query .= " ORDER BY b1.created_at DESC";
                }
                else if($sort == 'progress' && $order == 'asc')
                {            
                    $query .= " ORDER BY (b1.total_pages - b2.read_pages)";
                }
                else if($sort == 'progress' && $order == 'desc')
                {            
                    $query .= " ORDER BY (b1.total_pages - b2.read_pages) DESC";
                }
            }
            else
            {
                if($sort == 'date_added' && $order == 'asc')
                {            
                    $query .= " ORDER BY created_at";
                }
                else if($sort == 'date_added' && $order == 'desc')
                {            
                    $query .= " ORDER BY created_at DESC";
                }          
                else if($sort == 'progress' && $order == 'asc')
                {   
                    $query ="SELECT * FROM books b1 INNER JOIN books b2 on b1.id = b2.id WHERE b1.user_id='" . Auth::user()->id . "' ";        
                    $query .= "AND b1.isWishlistItem = '0' ORDER BY ((b1.read_pages / b2.total_pages)*100)";
                }
                else if($sort == 'progress' && $order == 'desc')
                {   
                    $query ="SELECT * FROM books b1 INNER JOIN books b2 on b1.id = b2.id WHERE b1.user_id='" . Auth::user()->id . "' ";        
                    $query .= "AND b1.isWishlistItem = '0' ORDER BY ((b1.read_pages / b2.total_pages)*100) DESC";
                }
            }
        }

        $book_list = DB::select($query);

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
        
        return view('books.index', [
            'book_list' => $book_list, 
            'num_book_found' => $num_book_found,
            'filtered_by' => $filter
        ]);
    }

    function show_from_model($id)
    {
        $book = Book::where([['id', $id], ['user_id', Auth::user()->id]])->first(); 

        if(is_null($book))
        {
            return view('books.show', ['response'=> 'No Book Found!', 'type' => 'NOT_FOUND']);
        }
        $book->edition_key = $book->book_id;

        $authors = Author::where('book_id', $id)->get();
        $book->authors = $authors;

        $publishers = Publisher::where('book_id', $id)->get();
        $book->publishers = $publishers;

        $subjects = Subject::where('book_id', $id)->get();
        $book->subjects = $subjects;

        $reviews = $this->retrieve_public_reviews($book);

        return view('books.show', ['book' => $book, 'type' => 'MODEL_DATA', 'reviews' => $reviews]);
    }

    function show_from_search_result($edition_key)
    {
        $search_controller = new SearchController();
        $search_result = $search_controller->search_by_edition_key($edition_key);

        if($search_result->response == "OK")
        {
            $reviews = $this->retrieve_public_reviews($search_result->book);
            return view('books.show', ['book' => $search_result->book, 'type' => 'SEARCH_DATA', 'reviews' => $reviews]);
        }
        return view('books.show', ['response' => $search_result->message, 'type' => 'NOT_FOUND']);
    }

    function create()
    {
        $book = array(  'title' => '',
                        'edition_key' => '',
                        'subtitle' => '',
                        'authors' => [],
                        'publishers' => [],
                        'subjects' => [],
                        'publish_date' => '',
                        'total_pages' => '',
                        'read_pages' => '',
                        'description' => '',
                        'cove_id' => '',
                        'comment' => '',
                        'public_comment' => '',
                        'cover_url' => ''
                    );      
        return view('books.create', ['book' => (object)$book]);
    }

    function create_with_data($edition_key)
    {
        $search_controller = new SearchController();
        $search_result = $search_controller->search_by_edition_key($edition_key);

        if($search_result->response == "OK")
        {
            return view('books.create', ['book' => $search_result->book]);
        }
        else
        {
            return redirect()->route('books.create');
        }
    }

    function store(Request $request)
    {
        $validator = $this->validate_request($request);
        
        //If validator fails, returns the data back to create form with error messages
        if($validator->status == "FAILED")
        {          
            return view('books.create', ['book' => $this->request_to_book_object($request), 'errors' => $validator->errors]);
        }

        //If edition key is empty, store the book without checking if it exists
        if(is_null($request->edition_key) || strlen($request->edition_key) == 0)
        {
            $this->save_book($request);
            return redirect()->route('books.index');
        } 
        
        //Checks to make sure same book is not stored twice
        $book = Book::where('user_id', Auth::user()->id)
                    ->where('book_id', $request->edition_key)
                    ->first();

        if(is_null($book)) 
        {
            $this->save_book($request);
            return redirect()->route('books.index');
        }
        else if($book->isWishlistItem == 1)
        {
            $book->isWishlistItem = 0;
            $book->save();
            return redirect()->route('books.index');
        }
        else
        {
            return view('books.create', [
                'book' => $this->request_to_book_object($request), 
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
        $validator = $this->validate_request($request);
        
        //If validator fails, returns the data back to create form with error messages
        if($validator->status == "FAILED")
        {      
            $book = $this->request_to_book_object($request);
            $book->id = $request->id;
            return view('books.edit', ['book' => $book, 'errors' => $validator->errors]);
        }

        if(Book::select('id')->where([['id', strip_tags($request->id)], ['user_id', Auth::user()->id]])->exists())
        {   
            $updated = $this->save_book($request, $request->id); 

            if($updated->response == 'OK')
            {
                return redirect()->route('books.show_from_model', ['id' => $updated->book_id]);
            }
        }
        return view('books.edit', [
            'book' => $this->request_to_book_object($request), 
            'errors' => [(object)['message' => 'This book does not exist in your library.']]
        ]);
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

    function update_read_pages(Request $request)
    {
        $request->validate(['book_id' => 'integer|required', 'read_pages' => 'required|integer']);
        $book = Book::where([['id', strip_tags($request->book_id)], ['user_id', Auth::user()->id]])->first(); ;

        if(is_null($book))
        {
            return json_encode(['response' => 'FAILED', 'message' => 'Invalid book id.']);
        }

        $book->read_pages = strip_tags($request->read_pages);
        $book->save();

        $message = '';
        if($book->total_pages == $book->read_pages)
        {
            $message = 'Congratulations! You have finished this book.';
        }
        else
        {
            $message = 'Almost there! You are ' . $book->total_pages - $book->read_pages . ' pages away from finshing this book.';
        }
         
        return json_encode(['response' => 'OK', 'message' => $message]);
    } 

    function get_book_file($file)
    {
        $book = Book::where([['book_file_path', strip_tags($file)], ['user_id', Auth::user()->id]])->first();
        
        if(!is_null($book))
        {
            $path = storage_path('app/books/'.$file);

            if (file_exists($path)) 
            {
                return response()->file($path, array('Content-Type' => 'application/pdf'));
            }
        }
        abort(404);
    }

    function read_book($id)
    {
        $book = Book::where([['id', strip_tags($id)], ['user_id', Auth::user()->id]])->first();

        if(!is_null($book))
        {
            return view('books.read', ['book_file_path' => $book->book_file_path]);
        }
        return view('books.index');
    }
    

    ////////////////////////////////////////////////////////////////////////////////////////////////////////
    //
    // UTILITY METHODS 
    //
    /////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * If $id == null, the function is called to store info of a new book
     * else if $id value is passed, the function is called to update an existing book 
     */
    private function save_book($request, $id = null)
    {
        $book = ($id === null) ? new Book() : Book::where([['id', strip_tags($id)], ['user_id', Auth::user()->id]])->first();

        if(is_null($book))
        {
            return (object)['response' => 'FAILED', 'message' => 'No Book found'];
        }

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

        if(!empty($request->file('book_file')))
        {
            $path = $request->file('book_file')->store('books');
            $book->book_file_path = explode('/', $path)[1];
            $book->has_pdf = 1;
        }
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
        for ($i = 1; $i <= 15; $i++) 
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
        for ($i = 1; $i <= 15; $i++) 
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
        for ($i = 1; $i <= 15; $i++) 
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
        return (object)['response' => 'OK', 'book_id' => $book->id];
    }

    private function retrieve_public_reviews($book)
    {
        $query = "SELECT books.public_comment AS comment, books.updated_at AS review_date,
                  users.name AS user_name, users.nickname AS user_nickname, users.use_nickname
                  FROM books INNER JOIN users on books.user_id = users.id WHERE LENGTH(books.public_comment) > 0 ";

        $title = addslashes($book->title);
        if(!empty($book->edition_key))
        {
            $query .= " AND (book_id = '" . $book->edition_key . "' OR title = '" . $title . "') ORDER BY books.updated_at";
        }
        else
        {
            $query .= " AND title = '" . $title . "' ORDER BY books.updated_at";
        }

        $reviews = DB::select($query);
        return $reviews;
    }

    private function validate_request($request)
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
            if(!is_numeric($request->total_pages) || $request->total_pages < 1 || $request->total_pages > 10000)
            {
                $isValidRequest = false;
                array_push($errors, (object)['message' => 'Number of pages must be an integer between 1 and 10000.']);
            }
        }
        else
        {
            $isValidRequest = false;
            array_push($errors, (object)['message' => 'Number of pages must be an integer between 1 and 10000.']);
        }
        
        if(strlen($request->read_pages) > 0)
        {
            if(!is_numeric($request->read_pages) || $request->read_pages < 0 || $request->read_pages > 10000)
            {
                $isValidRequest = false;
                array_push($errors, (object)['message' => 'Number of read pages must be an integer between 0 and 10000.']);
            }
        }
        
        if($request->read_pages > $request->total_pages)
        {
            $isValidRequest = false;
            array_push($errors, (object)['message' => 'Number of read pages cannot exceed total number of pages.']);
        }

        if(!empty($request->file('book_file')))
        {
            $file= $request->file('book_file');
            $fileExt = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
            $fileSize = $file->getSize() / 1024; //Byte -> KB

            if($fileExt != 'pdf')
            {
                $isValidRequest = false;
                array_push($errors, (object)['message' => 'The book file must be in PDF format.']);
            }

            if($fileSize > 20480) //Size greater than 20 mb
            {
                $isValidRequest = false;
                array_push($errors, (object)['message' => 'The book file size cannot be more than 20 MB.']);
            }
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

    private function request_to_book_object($request)
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
        for($i=1; $i<=15; $i++)
        {
            $a = 'author' . $i;
            if($request->exists($a))
            {
                array_push($authors, (object)['name' => $request->$a]);
            }
        }
        $book->authors = $authors;

        $publishers = array();
        for($i=1; $i<=15; $i++)
        {
            $p = 'publisher' . $i;

            if($request->exists($p))
            {
                array_push($publishers, (object)['name' => $request->$p]);
            }
        }
        $book->publishers = $publishers;

        $subjects = array();
        for($i=1; $i<=15; $i++)
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