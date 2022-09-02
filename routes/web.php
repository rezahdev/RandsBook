<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookReviewController;

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
Route::get('/book/{id}', [BookController::class, 'show_from_model'])->middleware('auth')->name('books.show_from_model');
Route::get('/search', [BookController::class, 'search'])->middleware('auth')->name('books.search');
Route::get('/search/{edition_key}', [BookController::class, 'show_from_search_result'])->middleware('auth')->name('books.show_from_search_result');
Route::get('/add', [BookController::class, 'create'])->middleware('auth')->name('books.create');
Route::get('/add/{edition_key}', [BookController::class, 'create_with_data'])->middleware('auth')->name('books.create_with_data');
Route::get('/book/{id}/edit', [BookController::class, 'edit'])->middleware('auth')->name('books.edit');
Route::get('/wishlist', [BookController::class, 'wishlist'])->middleware('auth')->name('books.wishlist');
Route::get('/profile', [ProfileController::class, 'index'])->middleware('auth')->name('profile.index');
Route::get('/profile/changePassword', [ProfileController::class, 'change_password'])->middleware('auth')->name('profile.change_password');
Route::get('/community/bookReviews', [BookReviewController::class, 'index'])->middleware('auth')->name('community.bookReview.index');
Route::get('/community/bookReviews/add', [BookReviewController::class, 'create'])->middleware('auth')->name('community.bookReview.create');


Route::post('/store', [BookController::class, 'store'])->middleware('auth')->name('books.store');
Route::post('/wishlist/add', [BookController::class, 'add_to_wishlist'])->middleware('auth')->name('books.add_to_wishlist');
Route::post('/community/bookReviews/store', [BookReviewController::class, 'store'])->middleware('auth')->name('community.bookReview.store');


Route::put('/update', [BookController::class, 'update'])->middleware('auth')->name('books.update');
Route::put('/updateReadPages', [BookController::class, 'update_read_pages'])->middleware('auth')->name('books.update_read_pages');
Route::put('/profile/updateName', [ProfileController::class, 'update_name'])->middleware('auth')->name('profile.update_name');
Route::put('/profile/updateNickname', [ProfileController::class, 'update_nickname'])->middleware('auth')->name('profile.update_nickname');
Route::put('/profile/updateEmail', [ProfileController::class, 'update_email'])->middleware('auth')->name('profile.update_email');
Route::put('/profile/updatePassword', [ProfileController::class, 'update_password'])->middleware('auth')->name('profile.update_password');
Route::put('/wishlistToLibrary', [BookController::class, 'wishlist_to_library'])->middleware('auth')->name('books.wishlist_to_library');


Route::delete('/delete/{id}', [BookController::class, 'delete'])->middleware('auth')->name('books.delete');
Route::delete('/wishlist/remove', [BookController::class, 'remove_from_wishlist'])->middleware('auth')->name('books.remove_from_wishlist');

require __DIR__.'/auth.php';
