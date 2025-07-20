@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-between mb-4">
        <div class="col-md-6">
            <h2>Tickets</h2>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('tickets.create') }}" class="btn btn-primary">Create Ticket</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Priority</th>
                        @if(auth()->user()->is_admin)
                        <th>User</th>
                        <th>Assigned To</th>
                        @endif
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                    <tr>
                        <td>{{ $ticket->id }}</td>
                        <td>{{ $ticket->title }}</td>
                        <td>
                            @if ($ticket->status == 'open')
                                <span class="badge bg-danger">{{$ticket->status}}</span>
                            @elseif (($ticket->status == 'in_progess'))
                                <span class="badge bg-warning">{{$ticket->status}}</span>
                            @else
                                <span class="badge bg-success">{{$ticket->status}}</span>
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
                        @if(auth()->user()->is_admin)
                        <td>{{ $ticket->user->name }}</td>
                        <td>{{ $ticket->assignedTo ? $ticket->assignedTo->name : 'Unassigned' }}</td>
                        @endif
                        <td>
                            <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-sm btn-info">View</a>
                            @can('update', $ticket)
                            <a href="{{ route('tickets.edit', $ticket->id) }}" class="btn btn-sm btn-primary">Edit</a>
                            @endcan
                            @can('delete', $ticket)
                            <form action="{{ route('tickets.destroy', $ticket->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
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