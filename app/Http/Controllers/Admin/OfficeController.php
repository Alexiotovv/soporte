<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Office;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    public function index()
    {
        $offices = Office::latest()->get();
        return view('admin.offices.index', compact('offices'));
    }

    public function create()
    {
        return view('admin.offices.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:offices',
        ]);

        Office::create($request->all());

        return redirect()->route('admin.offices.index')
            ->with('success', 'Oficina creada exitosamente.');
    }

    public function show(Office $office)
    {
        return view('admin.offices.show', compact('office'));
    }

    public function edit(Office $office)
    {
        return view('admin.offices.edit', compact('office'));
    }

    public function update(Request $request, Office $office)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:offices,name,'.$office->id,
        ]);

        $office->update($request->all());

        return redirect()->route('admin.offices.index')
            ->with('success', 'Oficina actualizada exitosamente.');
    }

    public function destroy(Office $office)
    {
        // Verificar si hay usuarios asociados antes de eliminar
        if($office->users()->count() > 0) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar la oficina porque tiene usuarios asociados.');
        }

        $office->delete();

        return redirect()->route('admin.offices.index')
            ->with('success', 'Oficina eliminada exitosamente.');
    }
}