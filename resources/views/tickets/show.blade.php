@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Ticket #{{ $ticket->id }}
                    <span class="float-right">
                        <span class="badge badge-{{ $ticket->status == 'open' ? 'success' : ($ticket->status == 'in_progress' ? 'warning' : 'secondary') }}">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </span>
                    </span>
                </div>

                <div class="card-body">
                    <h5 class="card-title">{{ $ticket->title }}</h5>
                    <p class="card-text">{{ $ticket->description }}</p>
                    
                    <div class="mb-3">
                        <strong>Priority:</strong>
                        <span class="badge badge-{{ $ticket->priority == 'high' ? 'danger' : ($ticket->priority == 'medium' ? 'warning' : 'info') }}">
                            {{ ucfirst($ticket->priority) }}
                        </span>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Created by:</strong> {{ $ticket->user->name }}
                    </div>
                    
                    @if($ticket->assignedTo)
                    <div class="mb-3">
                        <strong>Assigned to:</strong> {{ $ticket->assignedTo->name }}
                    </div>
                    @endif
                    
                    @if($ticket->response)
                    <div class="card mt-4">
                        <div class="card-header bg-light">
                            <strong>Response from support:</strong>
                        </div>
                        <div class="card-body">
                            <p>{{ $ticket->response }}</p>
                        </div>
                    </div>
                    @endif
                    
                    <div class="mt-4">
                        <a href="{{ route('tickets.index') }}" class="btn btn-secondary">Back to list</a>
                        @can('update', $ticket)
                        <a href="{{ route('tickets.edit', $ticket->id) }}" class="btn btn-primary">Edit</a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection