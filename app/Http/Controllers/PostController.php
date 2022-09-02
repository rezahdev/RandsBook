<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    function index()
    {
        $posts = Post::all();
        
        foreach($posts as $post)
        {
            $book = Book::find($post->book_id);
            $authors = Author::where('book_id', $book->id);
            $book->authors = $authors;
            $post->book = $book;
        }

        return view('community.bookReviews.index', ['posts' => $posts]);
    }
}
