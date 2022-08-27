<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Book;
use App\Models\User;

class ProfileController extends Controller
{
    function index()
    {
        $books = Book::where('user_id', Auth::user()->id)->get();

        $completedBookCount = 0;
        $inprogressBookCount = 0;
        $wishlistBookCount = 0;

        foreach($books as $book)
        {
            if($book->isWishlistItem == '0' && $book->total_pages == $book->read_pages)
            {
                $completedBookCount++;
            }
            else if($book->isWishlistItem == '0')
            {
                $inprogressBookCount++;
            }
            else
            {
                $wishlistBookCount++;
            }
        }
        return view('profile', [
            'user' => Auth::user(),
            'completedBookCount' => $completedBookCount,
            'inprogressBookCount' => $inprogressBookCount,
            'wishlistBookCount' => $wishlistBookCount     
        ]);
    }

    function update_name(Request $request)
    {return "OK";
        try{
            
        $name = strip_tags($request->name);
        
        $user = User::find(Auth::user()->id);
        
        $user->name = $name;
        $user->save();
        return json_encode(['response' => 'OK', 'message' => 'Your name has been successfully updated.']);
        }
        catch(\Exception $e)
        {
            return json_encode(['response' => 'FAILED', 'message' => $e->getMessage()]);
        }
    }
}
