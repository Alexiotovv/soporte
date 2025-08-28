@extends('layouts.app')



@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Ticket #{{ $ticket->id }}
                    <span class="float-right">
                        <span class="badge bg-{{ $ticket->status == 'open' ? 'warning' : ($ticket->status == 'in_progress' ? 'primary' : 'secondary') }}">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </span>
                    </span>
                </div>

                <div class="card-body">
                    <h5 class="card-title">{{ $ticket->title }}</h5>
                    <p class="card-text" id="description">{!! $ticket->description !!}</p>
                    
                    <div class="mb-3">
                        <strong>Prioridad:</strong>
                        <span class="badge bg-{{ $ticket->priority == 'high' ? 'danger' : ($ticket->priority == 'medium' ? 'warning' : 'info') }}">
                            {{ ucfirst($ticket->priority) }}
                        </span>
                    </div>                    
                    <div class="mb-3">
                        <strong>Fecha Solicitud:</strong> {{ $ticket->formatted_date }} hrs.
                    </div>
                    <div class="mb-3">
                        <strong>Tiempo Transcurrido:</strong> {{ $ticket->elapsed_time }}
                    </div>
                    <div class="mb-3">
                        <strong>Creado por:</strong> {{ $ticket->user->name }}
                    </div>
                    
                    @if($ticket->assignedTo)
                    <div class="mb-3">
                        <strong>Asignado a:</strong> {{ $ticket->assignedTo->name }}
                    </div>
                    @endif
                    
                    <div class="mt-4">
                        <a href="{{ route('tickets.index') }}" class="btn btn-light btn-sm">⬅️ Regresar a lista</a>
                        @can('update', $ticket)
                        <a href="{{ route('tickets.edit', $ticket->id) }}" class="btn btn-light btn-sm">✏️ Edit</a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




{{-- Añadido recientemente para la conversación --}}

<div class="card mt-4">
    <div class="card-header">Conversación</div>
    <div class="card-body" style="max-height: 300px; overflow-y: auto;">
        @forelse($ticket->messages as $msg)
            <div class="mb-3 p-2 border rounded 
                        {{ $msg->user_id == auth()->id() ? 'bg-light text-right' : 'bg-white' }}">
                <strong>{{ $msg->user->name }}</strong> 
                <small class="text-muted">{{ $msg->created_at->format('d/m/Y H:i:s') }}</small>
                <p class="mb-1">{{ $msg->message }}</p>
                @if($msg->file)
                    <a href="{{ Storage::url($msg->file) }}" target="_blank">📎 Archivo adjunto</a>
                @endif
            </div>
        @empty
            <p class="text-muted">No hay mensajes aún.</p>
        @endforelse
    </div>

    {{-- Formulario para responder --}}
    <div class="card-footer">
        <form action="{{ route('tickets.messages.store', $ticket->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <textarea name="message" class="form-control" placeholder="Escribe tu mensaje..." required></textarea>
            </div>
            <div class="form-group mt-2">
                <input type="file" name="file" class="form-control">
            </div>
            <button class="btn btn-outline-primary btn-sm mt-3">Enviar</button>
        </form>
    </div>
</div>


@endsection


