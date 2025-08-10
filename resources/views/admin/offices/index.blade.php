@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-between mb-4">
        <div class="col-md-6">
            <h5>Gestión de Oficinas</h5>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('admin.offices.create') }}" class="btn btn-light btn-sm">➕ Nueva Oficina</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            

            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Usuarios</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($offices as $office)
                    <tr>
                        <td>{{ $office->id }}</td>
                        <td>{{ $office->name }}</td>
                        <td>{{ $office->users_count }}</td>
                        <td>
                            <a href="{{ route('admin.offices.edit', $office->id) }}" 
                               class="btn btn-sm btn-light">✏️ Editar</a>
                            <form action="{{ route('admin.offices.destroy', $office->id) }}" 
                                  method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light"
                                        onclick="return confirm('¿Estás seguro de eliminar esta oficina?')">
                                    ❌ Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection