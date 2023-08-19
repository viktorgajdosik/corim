<?php

use App\Http\Controllers\ListingController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Listing;

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

// Common Resource Routes:
// index - Show all listings
// show - Show single listing
// create - Show form to create new listing
// store - Store new listing
// edit - Show form to edit listing
// update - Update listing
// destroy - Delete listing

// All Listings
Route::get('/', [ListingController::class, 'index']);

// Show Create Form
Route::get('/listings/create', [ListingController::class, 'create'])->middleware('auth');

// Store Listing Data
Route::post('/listings', [ListingController::class, 'store'])->middleware('auth');

// Show Edit Form
Route::get('/listings/{listing}/edit', [ListingController::class, 'edit'])->middleware('auth');

// Update Listing
Route::put('/listings/{listing}', [ListingController::class, 'update'])->middleware('auth');

// Delete Listing
Route::delete('/listings/{listing}', [ListingController::class, 'destroy'])->middleware('auth');

// Manage Listings
Route::get('/listings/manage', [ListingController::class, 'manage'])->middleware('auth');

// Show Single Listing Management
Route::get('/listings/show-manage/{listing}', [ListingController::class, 'showManage'])->middleware('auth');

// Show Single Listing
Route::get('/listings/{listing}', [ListingController::class, 'show'])->middleware('auth');

// Show Register/Create Form
Route::get('/register', [UserController::class, 'create'])->middleware('guest');

// Create New User
Route::post('/users', [UserController::class, 'store']);

// Log User Out
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth');

// Show Login Form
Route::get('/login', [UserController::class, 'login'])->name('login')->middleware('guest');

// Log In User
Route::post('/users/authenticate', [UserController::class, 'authenticate']);

// Show User Profile
Route::get('/users/profile', [UserController::class, 'show'])->middleware('auth');

// Show Edit Profile Page
Route::get('/users/edit-profile', [UserController::class, 'editProfile'])->middleware('auth')->name('users.edit-profile');

// Edit Profile
Route::put('/users/profile', [UserController::class, 'updateProfile'])->middleware('auth');

// Delete Profile
Route::delete('/users/profile', [UserController::class, 'deleteProfile'])->middleware('auth');

// Apply to a listing
Route::post('/listings/{listing}/apply', [ListingController::class, 'apply'])->middleware('auth')->name('listings.apply');

// Accept an application
Route::post('/listings/applications/{application}/accept', [ListingController::class, 'acceptApplication'])->middleware('auth')->name('listings.accept');

// Deny an application
Route::post('/listings/applications/{application}/deny', [ListingController::class, 'denyApplication'])->middleware('auth')->name('listings.deny');
