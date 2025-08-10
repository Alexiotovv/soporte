@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Editar Ticket #{{ $ticket->id }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('tickets.update', $ticket->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="title">Título</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $ticket->title) }}" required>
                            @error('title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="priority">Prioridad</label>
                            <select class="form-control @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                <option value="low" {{ old('priority', $ticket->priority) == 'low' ? 'selected' : '' }}>🔵 Bajo</option>
                                <option value="medium" {{ old('priority', $ticket->priority) == 'medium' ? 'selected' : '' }}>🟡 Medio</option>
                                <option value="high" {{ old('priority', $ticket->priority) == 'high' ? 'selected' : '' }}>🔴 Alto</option>
                            </select>
                            @error('priority')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Descripción</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description', $ticket->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        @if(auth()->user()->is_admin)
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="open" {{ old('status', $ticket->status) == 'open' ? 'selected' : '' }}>🟡 Abierto</option>
                                <option value="in_progress" {{ old('status', $ticket->status) == 'in_progress' ? 'selected' : '' }}>🔵 En Progreso</option>
                                <option value="closed" {{ old('status', $ticket->status) == 'closed' ? 'selected' : '' }}>⚫ Cerrado</option>
                            </select>
                            @error('status')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="assigned_to">Asignar A</label>
                            <select class="form-control @error('assigned_to') is-invalid @enderror" id="assigned_to" name="assigned_to">
                                <option value="">Unassigned</option>
                                @foreach($staff as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_to', $ticket->assigned_to) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('assigned_to')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="response">Respuesta</label>
                            <textarea class="form-control @error('response') is-invalid @enderror" id="response" name="response" rows="5">{{ old('response', $ticket->response) }}</textarea>
                            @error('response')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        @endif

                        <div class="form-group">
                            <label for="file">Archivo adjunto</label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file">
                            <small class="form-text text-muted">
                                Formatos permitidos: JPG, PNG, JPEG. Tamaño máximo: 5MB
                            </small>
                            @error('file')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            
                            @isset($ticket->file)
                                <div class="mt-2">
                                    <small>Archivo actual:</small>
                                    <a href="{{ $ticket->file_url }}" target="_blank" class="d-block">
                                        {{ $ticket->file_name }}
                                    </a>
                                </div>
                            @endisset
                        </div>

                        <br>
                         <a href="{{ route('tickets.index') }}" class="btn btn-light btn-sm">⬅️ Regresar a lista</a>
                        <button type="submit" class="btn btn-light btn-sm">🔄 Actualizar Ticket</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection