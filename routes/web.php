<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('dashboard');
})->middleware('auth')->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/books/add', function () {
    return view('books.add');
})->middleware(['auth'])->name('books.add');

Route::get('/wishlist', function () {
    return view('wishlist');
})->middleware(['auth'])->name('wishlist');

Route::resource('books', BookController::class)->middleware(['auth']);

require __DIR__.'/auth.php';
