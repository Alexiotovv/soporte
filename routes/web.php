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
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Password;

// Verificación de email
Auth::routes(['verify' => true]);


// Authentication Routes...
Route::get('login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);
 
    $status = Password::sendResetLink(
        $request->only('email')
    );
 
    return $status === Password::ResetLinkSent
        ? back()->with(['status' => __($status)])
        : back()->withErrors(['email' => __($status)]);
})->middleware('guest')->name('password.email');

Route::get('/reset-password/{token}', function (string $token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');

Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function (User $user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password),
                'remember_token' => Str::random(60),
            ])->save();
        }
    );

    return $status === Password::PASSWORD_RESET
                ? redirect()->route('login')->with('status', __($status))
                : back()->withErrors(['email' => [__($status)]]);
})->middleware('guest')->name('password.update');



//Registro Público
Route::get('/registro', [PublicRegisterController::class, 'showForm'])->name('public.register.form');
Route::post('/registro', [PublicRegisterController::class, 'register'])->name('public.register');

// ✅ Rutas de verificación de email
Auth::routes(['verify' => true]);

// O si no usas Auth::routes, puedes declararlas manualmente:

// Mostrar aviso después de registrarse
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// Verificar email desde el enlace
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/login'); // o la ruta que quieras después de verificar
})->middleware(['auth','signed'])->name('verification.verify');

// Reenviar enlace de verificación
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Se envió un nuevo enlace de verificación a tu correo.');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');




Route::post('/tickets/{ticket}/messages', [TicketMessageController::class, 'store'])
    ->name('tickets.messages.store')
    ->middleware('auth');

Route::middleware(['auth','verified'])->group(function () {
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
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

