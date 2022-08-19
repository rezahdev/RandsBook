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

Route::get('/', [BookController::class, 'index'])->middleware('auth')->name('books.index');
Route::get('/create', [BookController::class, 'create'])->middleware('auth')->name('books.create');
Route::post('/create/data', [BookController::class, 'create_with_data'])->middleware('auth')->name('books.create_with_data');
Route::get('/search', [BookController::class, 'search'])->middleware('auth')->name('books.search');
Route::get('/show/{id}', [BookController::class, 'show'])->middleware('auth')->name('books.show');
Route::post('/store', [BookController::class, 'store'])->middleware('auth')->name('books.store');

Route::get('/wishlist', function () {
    return view('wishlist');
})->middleware(['auth'])->name('wishlist');

require __DIR__.'/auth.php';
