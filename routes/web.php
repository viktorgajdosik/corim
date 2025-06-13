<?php

use App\Http\Controllers\ListingController;
use App\Http\Controllers\UserController;
use App\Livewire\SearchDropdown;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// **Register Livewire Component for Search**
Livewire::component('search-dropdown', SearchDropdown::class);

// **HOME PAGE ROUTE (SHOW DEFAULT LISTINGS VIA CONTROLLER)**
Route::get('/', [ListingController::class, 'index']); // Use controller
// **LISTINGS ROUTES**
Route::get('/listings/create', [ListingController::class, 'create'])->middleware(['auth', 'verified']);
Route::post('/listings', [ListingController::class, 'store'])->middleware('auth', 'verified');
Route::get('/listings/{listing}/edit', [ListingController::class, 'edit'])->middleware('auth', 'verified');
Route::put('/listings/{listing}', [ListingController::class, 'update'])->middleware('auth', 'verified');
Route::delete('/listings/{listing}', [ListingController::class, 'destroy'])->middleware('auth', 'verified');
Route::get('/listings/manage', [ListingController::class, 'manage'])
    ->middleware(['auth', 'verified'])
    ->name('listings.manage');
Route::get('/listings/show-manage/{listing}', [ListingController::class, 'showManage'])
    ->middleware(['auth', 'verified'])
    ->name('listings.show-manage');

    Route::get('/listings/{listing}', [ListingController::class, 'show'])
    ->middleware('auth', 'verified')
    ->name('listings.show');

// **PROFILE USER ROUTES**
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/users/profile', [UserController::class, 'show'])->name('users.profile');
    Route::get('/users/edit-profile', [UserController::class, 'editProfile'])->name('users.edit-profile');

    // **Delete User Profile**
    Route::delete('/users/profile', [UserController::class, 'deleteProfile'])->name('users.delete-profile');

    // **User Logout**
    Route::post('/logout', function (Request $request) {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('message', 'You have been logged out.');
    })->withoutMiddleware(['auth', 'verified'])->name('logout');
});

// **GUEST-ONLY ROUTES**
Route::middleware('guest')->group(function () {
    Route::get('/register', fn() => view('users.register'))->name('register');
    Route::get('/login', fn() => view('users.login'))->name('login');
    Route::get('/forgot-password', fn() => view('users.forgot-password'))->name('password.request');
    Route::get('/reset-password/{token}', fn($request) => view('users.reset-password', ['request' => $request]))->name('password.reset');
});

// **EMAIL VERIFICATION ROUTES**
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', fn() => view('users.verify-email'))->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/users/profile')->with('message', 'Email verified successfully.');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware(['throttle:6,1'])->name('verification.send');
});

// **OTHER USER ACTIONS**
Route::post('/listings/{listing}/apply', [ListingController::class, 'apply'])->middleware('auth', 'verified')->name('listings.apply');
Route::post('/listings/applications/{application}/accept', [ListingController::class, 'acceptApplication'])->middleware('auth', 'verified')->name('listings.accept');
Route::post('/listings/applications/{application}/deny', [ListingController::class, 'denyApplication'])->middleware('auth', 'verified')->name('listings.deny');
