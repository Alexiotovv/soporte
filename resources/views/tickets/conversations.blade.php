@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Mis conversaciones con respuestas</h4>
        <a href="{{ route('tickets.index') }}" class="btn btn-light btn-sm">⬅️ Volver a tickets</a>
    </div>

    @if($tickets->isEmpty())
        <div class="alert alert-light border">
            No tienes conversaciones con respuestas pendientes.
        </div>
    @else
        <div class="row g-3">
            @foreach($tickets as $ticket)
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <h5 class="mb-1">#{{ $ticket->id }} - {{ $ticket->title }}</h5>
                                    <p class="text-muted mb-2">{{ $ticket->category?->name ?? 'Sin categoría' }}</p>
                                </div>
                                <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-outline-primary btn-sm">Ver conversación</a>
                            </div>

                            @php
                                $replyMessages = $ticket->messages->where('user_id', '!=', auth()->id())->sortByDesc('created_at');
                            @endphp

                            @foreach($replyMessages->take(3) as $msg)
                                <div class="mt-3 p-2 border rounded bg-light">
                                    <strong>{{ $msg->user->name }}</strong>
                                    <small class="text-muted d-block">{{ $msg->created_at->format('d/m/Y H:i:s') }}</small>
                                    <p class="mb-0 mt-1">{{ $msg->message }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
