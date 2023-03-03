<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\BookReviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BookController::class, 'index'])
    ->middleware('auth')
    ->name('books.index');

Route::get('/book/{id}', [BookController::class, 'show_from_model'])
    ->middleware('auth')
    ->name('books.show_from_model');

Route::get('/search', [SearchController::class, 'search'])
    ->middleware('auth')
    ->name('books.search');

Route::get('/search/{edition_key}', [BookController::class, 'show_from_search_result'])
    ->middleware('auth')
    ->name('books.show_from_search_result');

Route::get('/add', [BookController::class, 'create'])
    ->middleware('auth')
    ->name('books.create');

Route::get('/add/{edition_key}', [BookController::class, 'create_with_data'])
    ->middleware('auth')
    ->name('books.create_with_data');

Route::get('/book/{id}/edit', [BookController::class, 'edit'])
    ->middleware('auth')
    ->name('books.edit');

Route::get('/wishlist', [WishlistController::class, 'index'])
    ->middleware('auth')
    ->name('books.wishlist');

Route::get('/profile', [ProfileController::class, 'index'])
    ->middleware('auth')
    ->name('profile.index');

Route::get('/profile/changePassword', [ProfileController::class, 'change_password'])
    ->middleware('auth')
    ->name('profile.change_password');

Route::get('/community/bookReviews', [BookReviewController::class, 'index'])
    ->middleware('auth')
    ->name('community.bookReview.index');

Route::get('/community/bookReviews/add', [BookReviewController::class, 'create'])
    ->middleware('auth')
    ->name('community.bookReview.create');

Route::get('/community/bookReviews/{id}/edit', [BookReviewController::class, 'edit'])
    ->middleware('auth')
    ->name('community.bookReview.edit');

Route::get('/book/{id}/read', [BookController::class, 'read_book'])
    ->middleware('auth')
    ->name('books.read_book');

Route::get('/files/{file}', [BookController::class, 'get_book_file'])
    ->middleware('auth');

Route::post('/store', [BookController::class, 'store'])
    ->middleware('auth')
    ->name('books.store');

Route::post('/wishlist/add', [WishlistController::class, 'store'])
    ->middleware('auth')
    ->name('books.add_to_wishlist');

Route::post('/community/bookReviews/store', [BookReviewController::class, 'store'])
    ->middleware('auth')
    ->name('community.bookReview.store');

Route::post('/community/bookReviews/like', [BookReviewController::class, 'like'])
    ->middleware('auth')
    ->name('community.bookReview.like');

Route::post('/community/bookReviews/save', [BookReviewController::class, 'save'])
    ->middleware('auth')
    ->name('community.bookReview.save');

Route::put('/update', [BookController::class, 'update'])
    ->middleware('auth')
    ->name('books.update');

Route::put('/updateReadPages', [BookController::class, 'update_read_pages'])
    ->middleware('auth')
    ->name('books.update_read_pages');

Route::put('/profile/updateName', [ProfileController::class, 'update_name'])
    ->middleware('auth')
    ->name('profile.update_name');

Route::put('/profile/updateNickname', [ProfileController::class, 'update_nickname'])
    ->middleware('auth')
    ->name('profile.update_nickname');

Route::put('/profile/updateEmail', [ProfileController::class, 'update_email'])
    ->middleware('auth')
    ->name('profile.update_email');

Route::put('/profile/updatePassword', [ProfileController::class, 'update_password'])
    ->middleware('auth')
    ->name('profile.update_password');

Route::put('/wishlistToLibrary', [WishlistController::class, 'wishlist_to_library'])
    ->middleware('auth')
    ->name('books.wishlist_to_library');

Route::put('/community/bookReviews/update', [BookReviewController::class, 'update'])
    ->middleware('auth')
    ->name('community.bookReview.update');

Route::delete('/delete/{id}', [BookController::class, 'delete'])
    ->middleware('auth')
    ->name('books.delete');

Route::delete('/wishlist/remove', [WishlistController::class, 'delete'])
    ->middleware('auth')
    ->name('books.remove_from_wishlist');

Route::delete('/community/bookReviews/unlike', [BookReviewController::class, 'unlike'])
    ->middleware('auth')
    ->name('community.bookReview.unlike');

Route::delete('/community/bookReviews/unsave', [BookReviewController::class, 'unsave'])
    ->middleware('auth')
    ->name('community.bookReview.unsave');

Route::delete('/community/bookReviews/delete', [BookReviewController::class, 'delete'])
    ->middleware('auth')
    ->name('community.bookReview.delete');

require __DIR__ . '/auth.php';
