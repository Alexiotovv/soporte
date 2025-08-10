<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function index(Request $request)
    {
       $status = $request->input('status', 'open'); // Valor por defecto: 'open'
    
        // Base query según tipo de usuario
        $query = Auth::user()->is_admin
            ? Ticket::with(['user', 'assignedTo'])
            : Ticket::where('user_id', Auth::id());
        
        // Aplicar filtro de estado si no es 'all'
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }
        
        $tickets = $query->latest()->get();
        
        // Obtener conteos para los filtros
        $countQuery = Auth::user()->is_admin 
            ? Ticket::query()
            : Ticket::where('user_id', Auth::id());
        
        $counts = $countQuery->selectRaw('status, count(*) as total')
                            ->groupBy('status')
                            ->pluck('total', 'status');
        
        return view('tickets.index', [
            'tickets' => $tickets,
            'status' => $status,
            'counts' => $counts
        ]);
    
    }

    public function create()
    {
        return view('tickets.create');
    }

   public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'priority' => 'required|in:low,medium,high',
                'file' => 'sometimes|file|max:5120|mimes:jpg,jpeg,png',
            ]);

            $data = $request->only(['title', 'description', 'priority']);
            $data['user_id'] = auth()->id();

            if ($request->hasFile('file')) {
                try {
                    $file = $request->file('file');
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $file->getClientOriginalExtension();
                    $timestamp = now()->format('YmdHis');
                    $fileName = "{$originalName}_{$timestamp}.{$extension}";
                    
                    $data['file'] = $file->storeAs('tickets', $fileName, 'public');
                } catch (\Exception $fileException) {
                    Log::error('Error al procesar archivo: '.$fileException->getMessage());
                    return redirect()->back()
                        ->with('error', 'Error al procesar el archivo adjunto. Por favor intente nuevamente.');
                }
            }

            Ticket::create($data);

            return redirect()->route('tickets.index')->with('success', 'Ticket creado satisfactoriamente.');

            } catch (\Illuminate\Validation\ValidationException $e) {
                // Capturar errores de validación
                return redirect()->back()
                    ->withErrors($e->validator)
                    ->withInput()
                    ->with('error', 'Por favor corrige los errores en el formulario.');

            } catch (\Exception $e) {
                // Capturar cualquier otro error
                Log::error('Error al actualizar ticket: '.$e->getMessage());
                return redirect()->back()
                    ->with('error', 'Ocurrió un error inesperado. Por favor intente nuevamente.');
            }
    }

    public function update(Request $request, Ticket $ticket)
    {
        try {
            // Definir reglas de validación
            $rules = [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'priority' => 'required|in:low,medium,high',
                'status' => 'nullable|in:open,in_progress,closed',
                'assigned_to' => 'nullable|exists:users,id',
                'response' => 'nullable|string',
            ];

            // Aplicar reglas de archivo solo si se está subiendo uno
            if ($request->hasFile('file')) {
                $rules['file'] = 'file|max:5120|mimes:jpg,jpeg,png';
            }

            // Validar ANTES de procesar el archivo
            $validatedData = $request->validate($rules);

            // Preparar datos para actualización
            $data = $request->only(['title', 'description', 'priority', 'status', 'assigned_to', 'response']);
            
            // Manejar archivo adjunto
            if ($request->hasFile('file')) {
                try {
                    // Eliminar archivo anterior si existe
                    if ($ticket->file) {
                        Storage::disk('public')->delete($ticket->file);
                    }
                    
                    // Generar nombre único para el nuevo archivo
                    $file = $request->file('file');
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $file->getClientOriginalExtension();
                    $timestamp = now()->format('YmdHis');
                    $fileName = "{$originalName}_{$timestamp}.{$extension}";
                    
                    // Guardar archivo
                    $data['file'] = $file->storeAs('tickets', $fileName, 'public');
                } catch (\Exception $fileException) {
                    Log::error('Error al procesar archivo: '.$fileException->getMessage());
                    return redirect()->back()
                        ->with('error', 'Error al procesar el archivo adjunto. Por favor intente nuevamente.');
                }
            }

            // Actualizar ticket
            $ticket->update($data);

            return redirect()->route('tickets.index')
                ->with('success', 'Ticket actualizado satisfactoriamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Capturar errores de validación
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Por favor corrige los errores en el formulario.');

        } catch (\Exception $e) {
            // Capturar cualquier otro error
            Log::error('Error al actualizar ticket: '.$e->getMessage());
            return redirect()->back()
                ->with('error', 'Ocurrió un error inesperado. Por favor intente nuevamente.');
        }
    }

    public function show(Ticket $ticket)
    {
        return view('tickets.show', compact('ticket'));
    }

    public function edit(Ticket $ticket)
    {
        $staff = User::where('is_admin', true)->get();
        return view('tickets.edit', compact('ticket', 'staff'));
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return redirect()->route('tickets.index')->with('success', 'Ticket eliminado satisfactoriamente.');
    }
}