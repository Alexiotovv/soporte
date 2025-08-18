<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use AuthenticatesUsers;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Intentar login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // if (is_null($user->email_verified_at)) {
            //     Auth::logout();
            //     return redirect()->route('login')->withErrors([
            //         'email' => 'Debes verificar tu correo electrónico antes de poder iniciar sesión.'
            //     ]);
            // }
            // Verificar status del usuario
            if (Auth::user()->status != 1) {
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'email' => 'Tu cuenta aún no ha sido aprobada, solicita a la oficina por favor.'
                ]);
            }

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}