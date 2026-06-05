@extends('layouts.app')

@section('content')
<style>
    .tickets-page {
        --tp-surface: #ffffff;
        --tp-surface-soft: #f6f8fb;
        --tp-border: #e6ebf2;
        --tp-text: #1f2a37;
        --tp-muted: #6b7280;
        --tp-accent: #0f766e;
        --tp-accent-soft: #e8f3f2;
    }

    .tickets-page {
        color: var(--tp-text);
    }

    .tickets-page .soft-card {
        border: 1px solid var(--tp-border);
        border-radius: 14px;
        background: var(--tp-surface);
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
    }

    .tickets-page .filter-wrap {
        gap: 8px;
    }

    .tickets-page .btn-soft {
        border: 1px solid #d9e2ec;
        background: #f9fbfd;
        color: #334155;
    }

    .tickets-page .table-clean {
        margin-bottom: 0;
    }

    .tickets-page .status-pill,
    .tickets-page .priority-pill {
        border-radius: 999px;
        font-weight: 600;
        padding: 0.35rem 0.6rem;
        font-size: 0.75rem;
    }

    .tickets-page .status-open,
    .tickets-page .priority-medium {
        background: #ffd96a;
        color: #5f4300;
    }

    .tickets-page .status-progress {
        background: #b9d5ff;
        color: #123f7a;
    }

    .tickets-page .status-closed {
        background: #cdd7e5;
        color: #2f3a4a;
    }

    .tickets-page .priority-low {
        background: #b9e4ff;
        color: #0c4660;
    }

    .tickets-page .priority-high {
        background: #ffbcbc;
        color: #741111;
    }

    @media (max-width: 768px) {
        .tickets-page .filter-wrap {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
</style>

<div class="container-fluid px-2 px-md-3 tickets-page">
    <div class="row justify-content-between mb-4">
        <div class="col-md-6">
            <h5 class="mb-1">Tickets</h5>
            <small class="text-muted">Listado general de solicitudes de soporte.</small>
        </div>
        <div class="col-md-6 text-md-right mt-3 mt-md-0">
            <a href="{{ route('tickets.create') }}" class="btn btn-soft btn-sm">🎫 Crear Ticket</a>
        </div>
    </div>


    <!-- Botones de filtro -->
    <div class="btn-group mb-4 filter-wrap" role="group" aria-label="Filtros de ticket">
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
    <div class="card soft-card">
        <div class="card-body">
            <div class="table-responsive">
            <table class="table table-hover table-clean" id="tickets">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Categoria</th>
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
                            @if($ticket->category)
                                <span class="badge" style="background-color: {{ $ticket->category->color }}; color: #fff;">
                                    {{ $ticket->category->name }}
                                </span>
                            @else
                                <span class="text-muted">Sin categoria</span>
                            @endif
                        </td>
                        <td>
                            @if ($ticket->status == 'open')
                                <span class="badge status-pill status-open">abierto</span>
                            @elseif ($ticket->status == 'in_progress')
                                <span class="badge status-pill status-progress">en progreso</span>
                            @else
                                <span class="badge status-pill status-closed">cerrado</span>
                            @endif
                        </td>
                        <td>
                            @if ($ticket->priority == 'low')
                                <span class="badge priority-pill priority-low">bajo</span>
                            @elseif ($ticket->priority == 'medium')
                                <span class="badge priority-pill priority-medium">medio</span>
                            @elseif ($ticket->priority == 'high')
                                <span class="badge priority-pill priority-high">alto</span>
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
                                <a href="{{ $ticket->file_url }}" target="_blank" class="btn btn-sm btn-soft">
                                    📎 {{ $ticket->file_name }}
                                </a>
                            @else
                                <span class="text-muted">Sin archivo</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-sm btn-soft">📋 Ver</a>
                            
                            @if(auth()->user()->is_admin)
                                <a href="{{ route('tickets.edit', $ticket->id) }}" class="btn btn-sm btn-soft">✏️ Editar</a>
                            @endif

                            @can('delete', $ticket)
                            <form action="{{ route('tickets.destroy', $ticket->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-soft" onclick="return confirm('Are you sure?')">🗑️ Eliminar</button>
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