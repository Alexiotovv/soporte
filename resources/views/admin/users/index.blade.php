@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-between mb-4">
        <div class="col-md-6">
            <h5>Gestión de Usuarios</h5>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('admin.users.create') }}" class="btn btn-light btn-sm">👤 Crear Usuario</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover" id="usuarios">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Oficina</th>
                        <th>Rol</th>
                        <th>Status</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? 'N/A' }}</td>
                        <td>{{ $user->office->name ?? 'Sin oficina' }}</td>
                        <td>
                            @if ($user->is_admin == 1)
                                <span class="badge bg-info text-dark">Administrator</span>
                            @elseif($user->is_admin == 2)
                                <span class="badge bg-light text-dark">Support Team</span>
                            @else
                                <span class="badge bg-light text-dark">Regular User</span>
                            @endif
                        </td>
                        <td>
                            @if($user->status == 1)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-secondary">Inhabilitado</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-light">✏️ Edit</a>
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light" onclick="return confirm('Are you sure?')">❌ Delete</button>
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
@section('scripts')
     <script>
        $(document).ready(function () {
            $('#usuarios').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                }
            });
        });
    </script>
@endsection