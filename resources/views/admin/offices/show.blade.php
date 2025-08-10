@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Detalles de la Oficina: {{ $office->name }}</div>

                <div class="card-body">
                    <div class="mb-3">
                        <strong>ID:</strong> {{ $office->id }}
                    </div>
                    <div class="mb-3">
                        <strong>Nombre:</strong> {{ $office->name }}
                    </div>
                    <div class="mb-3">
                        <strong>Usuarios asociados:</strong> {{ $office->users_count }}
                    </div>

                    <h5 class="mt-4">Usuarios en esta oficina:</h5>
                    @if($office->users->count() > 0)
                        <ul class="list-group">
                            @foreach($office->users as $user)
                                <li class="list-group-item">
                                    {{ $user->name }} ({{ $user->email }})
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="alert alert-info">
                            No hay usuarios asignados a esta oficina.
                        </div>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('admin.offices.edit', $office->id) }}" 
                           class="btn btn-primary">Editar</a>
                        <a href="{{ route('admin.offices.index') }}" 
                           class="btn btn-secondary">Volver</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection