<?php

use App\Http\Controllers\TicketController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Admin\OfficeController;
use App\Http\Controllers\Admin\PublicRegisterController;
use App\Http\Controllers\TicketMessageController;
use App\Models\Ticket;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon; 

// Authentication Routes...
Route::get('login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Registration Routes...
// Route::get('register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
// Route::post('register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

// Password Reset Routes...
// Route::get('password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
// Route::post('password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
// Route::get('password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
// Route::post('password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

//Registro Público
Route::get('/registro', [PublicRegisterController::class, 'showForm'])->name('public.register.form');
Route::post('/registro', [PublicRegisterController::class, 'register'])->name('public.register');


Route::post('/tickets/{ticket}/messages', [TicketMessageController::class, 'store'])
    ->name('tickets.messages.store')
    ->middleware('auth');

Route::middleware(['auth'])->group(function () {
    // Tickets routes
    Route::get('/tickets/ultimo', [TicketController::class, 'ultimo']);
    Route::post('/tickets/marcar-visto/{id}', [TicketController::class, 'marcarVisto']);
    Route::resource('tickets', TicketController::class);

    // Admin routes
    Route::prefix('admin')->group(function () {
        Route::resource('users', UserController::class)->names([
            'index'   => 'admin.users.index',
            'create'  => 'admin.users.create',
            'store'   => 'admin.users.store',
            'show'    => 'admin.users.show',
            'edit'    => 'admin.users.edit',
            'update'  => 'admin.users.update',
            'destroy' => 'admin.users.destroy',
        ]);
        Route::resource('offices', OfficeController::class)->names([
            'index' => 'admin.offices.index',
            'create' => 'admin.offices.create',
            'store' => 'admin.offices.store',
            'show' => 'admin.offices.show',
            'edit' => 'admin.offices.edit',
            'update' => 'admin.offices.update',
            'destroy' => 'admin.offices.destroy'
        ]);
    });
});


// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/', function () {
    return redirect()->route('tickets.index');
});
