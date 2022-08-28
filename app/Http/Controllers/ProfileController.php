<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        return view('profile.index', [
            'user' => Auth::user(),
            'completedBookCount' => $completedBookCount,
            'inprogressBookCount' => $inprogressBookCount,
            'wishlistBookCount' => $wishlistBookCount     
        ]);
    }

    function update_name(Request $request)
    {
        $request->validate(['name' => 'required']);
        $name = strip_tags($request->name);     
        
        User::where('id', Auth::user()->id)->update(['name' => $name]);
            
        return redirect()->route('profile.index');
    }

    function update_email(Request $request)
    {
        $request->validate(['email' => 'required|email|unique:users']);
        $email = strip_tags($request->email);     
            
        User::where('id', Auth::user()->id)->update(['email' => $email]);

        return redirect()->route('profile.index');
    }

    function update_nickname(Request $request)
    {
        $request->validate(['nickname' => 'required|unique:users']);
        $nickname = strip_tags($request->nickname);   
        $use_nickname = $request->use_nickname == '1' ? '1' : '0';  
        
        User::where('id', Auth::user()->id)->update(['nickname' => $nickname, 'use_nickname' => $use_nickname]);
            
        return redirect()->route('profile.index');
    }

    function change_password()
    {
       return view('profile.changePassword');
    }

    function update_password(Request $request)
    {
        $request->validate(['password' => 'required|string|max:30|min:8|confirmed']);

        $user = User::find(Auth::user()->id);
        if(Hash::check($request->current_password, $user->password))
        {
            $password = Hash::make($request->password);
            User::where('id', Auth::user()->id)->update(['password' => $password]);
            return view('profile.changePassword', ['status' => 'PASSWORD_CHANGED']);
        }
        else
        {
            return view('profile.changePassword')->withErrors(['error' => 'Invalid password.']);
        }
    }
}
