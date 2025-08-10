@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-between mb-4">
        <div class="col-md-6">
            <h5>Tickets</h5>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('tickets.create') }}" class="btn btn-light btn-sm">🎫 Crear Ticket</a>
        </div>
    </div>


    <!-- Botones de filtro -->
    <div class="btn-group mb-4" role="group">
        <a href="{{ route('tickets.index', ['status' => 'all']) }}" 
           class="btn btn-{{ !request()->has('status') || request('status') === 'all' ? 'info' : 'outline-info' }} btn-sm">
            Todos ({{ array_sum($counts->toArray()) }})
        </a>
        <a href="{{ route('tickets.index', ['status' => 'open']) }}" 
           class="btn btn-{{ request('status') == 'open' ? 'warning' : 'outline-warning' }} btn-sm">
            Abiertos ({{ $counts['open'] ?? 0 }})
        </a>
        <a href="{{ route('tickets.index', ['status' => 'in_progress']) }}" 
           class="btn btn-{{ request('status') == 'in_progress' ? 'primary' : 'outline-primary' }} btn-sm">
            En Progreso ({{ $counts['in_progress'] ?? 0 }})
        </a>
        <a href="{{ route('tickets.index', ['status' => 'closed']) }}" 
           class="btn btn-{{ request('status') == 'closed' ? 'secondary' : 'outline-secondary' }} btn-sm">
            Cerrados ({{ $counts['closed'] ?? 0 }})
        </a>
    </div>



    <div class="card">
        <div class="card-body">
            <table class="table table-hover" id="tickets">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Status</th>
                        <th>Prioridad</th>
                        <th>Fecha Solicitud</th>
                        <th>Tiempo Transcurrido</th>
                        @if(auth()->user()->is_admin)
                            <th>User</th>
                            <th>Asignado A</th>
                        @endif
                        <th>Archivo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                    <tr>
                        <td>{{ $ticket->id }}</td>
                        <td>{{ $ticket->title }}</td>
                        <td>
                            @if ($ticket->status == 'open')
                                <span class="badge bg-warning">{{$ticket->status}}</span>
                            @elseif ($ticket->status == 'in_progress')
                                <span class="badge bg-primary">{{$ticket->status}}</span>
                            @else
                                <span class="badge bg-secondary">{{$ticket->status}}</span>
                            @endif
                        </td>
                        <td>
                            @if ($ticket->priority == 'low')
                                <span class="badge bg-info">{{$ticket->priority}}</span>
                            @elseif ($ticket->priority == 'medium')
                                <span class="badge bg-warning text-dark">{{$ticket->priority}}</span>
                            @elseif ($ticket->priority == 'high')
                                <span class="badge bg-danger">{{$ticket->priority}}</span>
                            @endif
                        </td>
                        <td>{{ $ticket->formatted_date }}</td>
                        <td>{{ $ticket->elapsed_time }}</td>
                        @if(auth()->user()->is_admin)
                            <td>{{ $ticket->user->name }}</td>
                            <td>{{ $ticket->assignedTo ? $ticket->assignedTo->name : 'Unassigned' }}</td>
                        @endif
                        <td>
                            @if($ticket->file)
                                <a href="{{ $ticket->file_url }}" target="_blank" class="btn btn-sm btn-light">
                                    📎 {{ $ticket->file_name }}
                                </a>
                            @else
                                <span class="text-muted">Sin archivo</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-sm btn-light">📋 View</a>
                            @can('update', $ticket)
                            <a href="{{ route('tickets.edit', $ticket->id) }}" class="btn btn-sm btn-light">✏️ Edit</a>
                            @endcan
                            @can('delete', $ticket)
                            <form action="{{ route('tickets.destroy', $ticket->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light" onclick="return confirm('Are you sure?')">🗑️ Delete</button>
                            </form>
                            @endcan
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
            $('#tickets').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                }
            });
        });
    </script>
@endsection