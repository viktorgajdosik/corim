<?php

use App\Http\Controllers\ListingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TaskController;
use Livewire\Livewire;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// **Register Livewire Component for Search**
Livewire::component('search-dropdown', \App\Livewire\SearchDropdown::class);

// **HOME PAGE ROUTE (SHOW DEFAULT LISTINGS VIA CONTROLLER)**
Route::get('/', [ListingController::class, 'index']); // Use controller

// **LISTINGS ROUTES**

// Create/store — any authenticated & verified user may create
Route::get('/listings/create', [ListingController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('listings.create');

// Edit/destroy — AUTHOR ONLY
Route::get('/listings/{listing}/edit', [ListingController::class, 'edit'])
    ->middleware(['auth', 'verified', 'can:authorOnly,listing'])
    ->name('listings.edit');

Route::delete('/listings/{listing}', [ListingController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'can:authorOnly,listing'])
    ->name('listings.destroy');

// Author’s dashboards/pages (list of their listings + single manage view)
Route::get('/listings/manage', [ListingController::class, 'manage'])
    ->middleware(['auth', 'verified'])
    ->name('listings.manage');

Route::get('/listings/show-manage/{listing}', [ListingController::class, 'showManage'])
    ->middleware(['auth', 'verified', 'can:authorOnly,listing'])
    ->name('listings.show-manage');

// Participant-facing show — NOT author
Route::get('/listings/{listing}', function (\App\Models\Listing $listing) {
    // If current user is the author, redirect to show-manage
    if (auth()->check() && $listing->user_id === auth()->id()) {
        return redirect()->route('listings.show-manage', $listing);
    }

    // Otherwise call the normal show action
    return app(\App\Http\Controllers\ListingController::class)->show($listing);
})
->middleware(['auth', 'verified'])
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

// Notifications (auth + verified)
Route::get('/notifications', fn () => view('notifications.index'))
    ->middleware(['auth','verified'])
    ->name('notifications.index');
