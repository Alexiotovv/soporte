<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // $users = User::latest()->get();
        $users = User::with('office')->latest()->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $offices = Office::all();
        return view('admin.users.create', compact('offices'));
    }

    public function store(Request $request)
    {   

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:5|confirmed',
            'role' => 'required|in:0,1,2', 
            'phone' => 'nullable|string|max:20', // Validación para teléfono
            'office_id' => 'nullable|exists:offices,id' // Validación para oficina
        ]);
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->role,
            'phone' => $request->phone,
            'office_id' => $request->office_id
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $offices = Office::all();
        return view('admin.users.edit', compact('user','offices'));
    }

    public function update(Request $request, User $user)
    {
              
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:0,1,2',
            'phone' => 'nullable|string|max:20', // Validación para teléfono
            'office_id' => 'nullable|exists:offices,id' // Validación para oficina
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'is_admin' => $request->role,
            'phone' => $request->phone,
            'office_id' => $request->office_id
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado correctamente.');
    }
}