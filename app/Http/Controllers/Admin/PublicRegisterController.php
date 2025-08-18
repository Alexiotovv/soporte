<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class PublicRegisterController extends Controller
{
    public function showForm()
    {
        return view('auth.public-register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone'    => 'nullable|string|max:20'
        ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_admin' => 0, // Usuario normal cliente
                'phone' => $request->phone,
                'status' => 1 // 0 Pendiente 1 Aprobado
            ]);

            // Disparar evento de registro
            event(new Registered($user));

            // Autenticar al usuario (opcional)
            // auth()->login($user);

            return redirect()->route('login')->with('success', 'Se ha enviado un enlace de verificación a tu correo electrónico. Por favor verifica tu email antes de iniciar sesión.');
        
        
        // $request->validate([
        //     'name'     => 'required|string|max:255',
        //     'email'    => 'required|string|email|max:255|unique:users',
        //     'password' => 'required|string|min:5|confirmed',
        //     'phone'    => 'nullable|string|max:20'
        // ]);

        // User::create([
        //     'name'     => $request->name,
        //     'email'    => $request->email,
        //     'password' => Hash::make($request->password),
        //     'is_admin' => 0, // Usuario normal cliente
        //     'phone'    => $request->phone,
        //     'status'   => 0  // 0 Pendiente  1 Aprovado
        // ]);

        // return redirect()->route('login')->with('success', 'Tu registro ha sido enviado. Espera aprobación.');
    }
}
