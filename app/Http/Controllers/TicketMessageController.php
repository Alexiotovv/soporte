<?php

// app/Http/Controllers/TicketMessageController.php
namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TicketMessageController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        $request->validate([
            'message' => 'required|string',
            'file' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf,doc,docx',
        ]);

        $data = [
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = now()->format('YmdHis') . '_' . $file->getClientOriginalName();
            $data['file'] = $file->storeAs('ticket_messages', $fileName, 'public');
        }

        TicketMessage::create($data);

        return redirect()->route('tickets.show', $ticket->id)
                         ->with('success', 'Mensaje enviado.');
    }

    
}
