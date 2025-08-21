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

    <div id="sse-data" style="font-family: monospace; margin: 20px;"></div>

    <div id="alerta-ticket" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050; display:none;">
        <!-- Aquí se insertará el contenido del alert -->
    </div>
    <div class="card">
        <div class="card-body">
            <table class="table table-hover" id="tickets">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Estado</th>
                        <th>Prioridad</th>
                        <th>Fecha Solicitud</th>
                        <th>Tiempo Transcurrido</th>
                        @if(auth()->user()->is_admin)
                            <th>Usuario</th>
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
                                <span class="badge bg-warning">abierto</span>
                            @elseif ($ticket->status == 'in_progress')
                                <span class="badge bg-primary">en progreso</span>
                            @else
                                <span class="badge bg-secondary">cerrado</span>
                            @endif
                        </td>
                        <td>
                            @if ($ticket->priority == 'low')
                                <span class="badge bg-info">bajo</span>
                            @elseif ($ticket->priority == 'medium')
                                <span class="badge bg-warning text-dark">medio</span>
                            @elseif ($ticket->priority == 'high')
                                <span class="badge bg-danger">alto</span>
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
                            
                            @if(auth()->user()->is_admin)
                                <a href="{{ route('tickets.edit', $ticket->id) }}" class="btn btn-sm btn-light">✏️ Edit</a>
                            @endif

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


<audio id="myAudio">
        <source src="{{ asset('storage/ticket_sound/tono_1.mp3') }}" type="audio/mp3">
</audio>

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            function reproducirSonido() {
                var audio = document.getElementById("myAudio");
                audio.play();
            }
            // Objeto para rastrear los tickets ya mostrados
            let ticketsMostrados = {};

            function verificarTickets() {
                $.get('/tickets/ultimo', function(data) {
                    // Si es un array de tickets (para compatibilidad con ambas versiones)
                    const tickets = Array.isArray(data) ? data : (data.id ? [data] : []);
                    
                    tickets.forEach(ticket => {
                        // Si el ticket no ha sido mostrado aún
                        if (ticket.id && !ticketsMostrados[ticket.id]) {
                            ticketsMostrados[ticket.id] = true;
                            reproducirSonido();
                            mostrarAlerta(ticket);
                            setTimeout(function() {
                                vozAlerta(ticket.user.name +" necesita apoyo.");
                            },3000);

                        }
                    });
                });
            }

            function vozAlerta(texto) {
                var synthesis = new SpeechSynthesisUtterance(texto);
                synthesis.lang = 'es-EN';
                synthesis.rate = 1; // Velocidad normal
                synthesis.pitch = 1.0; // Tono normal
                window.speechSynthesis.speak(synthesis);
            }

            function mostrarAlerta(ticket) {
                let html = `
                    <div class="alert alert-warning alert-dismissible fade show shadow-lg mb-3" role="alert" style="z-index: 9999;" data-ticket-id="${ticket.id}">
                        <div class="alert-header d-flex justify-content-between align-items-center mb-2">
                            <h4 class="mb-0">🚨 Ticket Abierto</h4>
                            <button type="button" class="btn-close" onclick="cerrarAlerta(${ticket.id}, $(this).closest('.alert'));"></button>
                        </div>
                        <div class="alert-body">
                            <p><strong>N° Ticket:</strong> ${ticket.id}</p>
                            <p><strong>Usuario:</strong> ${ticket.user ? ticket.user.name : 'Desconocido'}</p>
                            <p><strong>Descripción:</strong> ${ticket.description}</p>
                        </div>
                        <div class="alert-footer mt-3 text-end">
                            <button class="btn btn-success btn-sm" onclick="cerrarAlerta(${ticket.id}, $(this).closest('.alert'));">Aceptar</button>
                        </div>
                    </div>
                `;

                $('#alerta-ticket').prepend(html).fadeIn();
            }
        
            window.cerrarAlerta = function(ticketId, alertElement) {
                $.post(`/tickets/marcar-visto/${ticketId}`, {
                    _token: '{{ csrf_token() }}',
                    _method: 'POST'
                }, function() {
                    alertElement.fadeOut(function() {
                        $(this).remove();
                    });
                    // Opcional: eliminar del objeto de tickets mostrados si quieres que pueda reaparecer
                    // delete ticketsMostrados[ticketId];
                }).fail(function() {
                    alert('Error al actualizar el ticket');
                });
            }

            // Ejecutar cada 5 segundos
            const userRole = {{ auth()->user()->is_admin }};
                if (userRole === 1 || userRole === 2) {
                    setInterval(verificarTickets, 5000);
                    // Ejecutar inmediatamente al cargar la página
                    verificarTickets();
                }
            });
    </script>
    

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