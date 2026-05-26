@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 mb-3">
            <h4 class="mb-1">Ubicacion del Equipo de Soporte</h4>
            <p class="text-muted mb-0">Visualiza en tiempo real a los usuarios con rol soporte que tengan coordenadas habilitadas.</p>
        </div>

        <div class="col-12 mb-3">
            <div class="card">
                <div class="card-body d-flex flex-wrap align-items-center gap-2">
                    <label for="office-filter" class="mb-0 fw-semibold">Filtrar por oficina</label>
                    <select id="office-filter" class="form-select" style="max-width: 320px;">
                        <option value="">Todas las oficinas</option>
                        @foreach($offices as $office)
                            <option value="{{ $office->id }}">{{ $office->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="col-lg-8 mb-3">
            <div class="card">
                <div class="card-body p-2">
                    <div id="admin-support-map" style="height: 520px; border-radius: 12px; overflow: hidden;"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-header">Soporte Activo</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Oficina</th>
                                    <th>Estado</th>
                                    <th>Ultima</th>
                                </tr>
                            </thead>
                            <tbody id="support-users-body">
                                <tr><td colspan="4" class="text-center text-muted p-3">Cargando ubicaciones...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">Historial Reciente del Tecnico Seleccionado</div>
                <div class="card-body">
                    <p id="history-title" class="text-muted mb-2">Selecciona un marcador en el mapa para ver sus ultimos puntos.</p>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Latitud</th>
                                    <th>Longitud</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody id="history-body">
                                <tr><td colspan="4" class="text-center text-muted p-3">Sin historial seleccionado.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        .support-user-marker {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #1e6b52;
            border: 3px solid #ffffff;
            box-shadow: 0 0 0 3px rgba(30, 107, 82, 0.22);
        }

        .admin-user-marker {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #1f4f9a;
            border: 3px solid #ffffff;
            box-shadow: 0 0 0 3px rgba(31, 79, 154, 0.24);
        }
    </style>
@endsection

@section('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        (function () {
            const map = L.map('admin-support-map').setView([-12.0464, -77.0428], 11);
            const usersBody = document.getElementById('support-users-body');
            const historyBody = document.getElementById('history-body');
            const historyTitle = document.getElementById('history-title');
            const officeFilter = document.getElementById('office-filter');
            const markers = {};
            let historyPolyline = null;
            let adminMarker = null;
            let hasAdminCenter = false;
            const supportIcon = L.divIcon({
                className: 'support-user-marker-wrap',
                html: '<div class="support-user-marker"></div>',
                iconSize: [24, 24],
                iconAnchor: [12, 12],
            });
            const adminIcon = L.divIcon({
                className: 'admin-user-marker-wrap',
                html: '<div class="admin-user-marker"></div>',
                iconSize: [24, 24],
                iconAnchor: [12, 12],
            });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            function centerToAdminLocation() {
                if (!navigator.geolocation) {
                    return;
                }

                navigator.geolocation.getCurrentPosition(function (position) {
                    const lat = Number(position.coords.latitude.toFixed(7));
                    const lng = Number(position.coords.longitude.toFixed(7));
                    const latlng = [lat, lng];

                    if (!adminMarker) {
                        adminMarker = L.marker(latlng, { icon: adminIcon }).addTo(map);
                    } else {
                        adminMarker.setLatLng(latlng);
                    }

                    adminMarker.bindPopup('Tu ubicacion (admin)');

                    if (!hasAdminCenter) {
                        map.setView(latlng, 15);
                        hasAdminCenter = true;
                    }
                });
            }

            function clearRemovedMarkers(activeIds) {
                Object.keys(markers).forEach(function (id) {
                    if (!activeIds.includes(Number(id))) {
                        map.removeLayer(markers[id]);
                        delete markers[id];
                    }
                });
            }

            function formatDate(dateText) {
                if (!dateText) {
                    return '-';
                }

                const date = new Date(dateText.replace(' ', 'T'));
                if (Number.isNaN(date.getTime())) {
                    return dateText;
                }

                return date.toLocaleString();
            }

            function renderTable(users) {
                if (!users.length) {
                    usersBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted p-3">No hay usuarios soporte activos.</td></tr>';
                    return;
                }

                usersBody.innerHTML = users.map(function (user) {
                    const state = user.is_sharing && user.latitude !== null && user.longitude !== null
                        ? '<span class="badge bg-success">En linea</span>'
                        : '<span class="badge bg-secondary">Sin coordenadas</span>';

                    return '<tr>' +
                        '<td>' + user.name + '</td>' +
                        '<td>' + user.office_name + '</td>' +
                        '<td>' + state + '</td>' +
                        '<td>' + formatDate(user.last_seen_at) + '</td>' +
                    '</tr>';
                }).join('');
            }

            function clearHistoryLine() {
                if (historyPolyline) {
                    map.removeLayer(historyPolyline);
                    historyPolyline = null;
                }
            }

            function renderHistory(userName, points) {
                historyTitle.textContent = 'Historial reciente de: ' + userName;

                if (!points.length) {
                    historyBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted p-3">Sin puntos recientes para este tecnico.</td></tr>';
                    clearHistoryLine();
                    return;
                }

                historyBody.innerHTML = points.map(function (point, index) {
                    return '<tr>' +
                        '<td>' + (index + 1) + '</td>' +
                        '<td>' + point.latitude + '</td>' +
                        '<td>' + point.longitude + '</td>' +
                        '<td>' + formatDate(point.created_at) + '</td>' +
                    '</tr>';
                }).join('');

                const polylinePoints = points.map(function (point) {
                    return [Number(point.latitude), Number(point.longitude)];
                }).reverse();

                clearHistoryLine();
                historyPolyline = L.polyline(polylinePoints, {
                    color: '#1e6b52',
                    weight: 4,
                    opacity: 0.8,
                }).addTo(map);
            }

            function loadHistory(userId, userName) {
                fetch('/admin/support-locations/' + userId + '/history', {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('No se pudo cargar historial.');
                    }

                    return response.json();
                })
                .then(function (payload) {
                    const history = Array.isArray(payload.history) ? payload.history : [];
                    renderHistory(userName, history);
                })
                .catch(function () {
                    historyTitle.textContent = 'Historial reciente';
                    historyBody.innerHTML = '<tr><td colspan="4" class="text-center text-danger p-3">No se pudo cargar el historial.</td></tr>';
                    clearHistoryLine();
                });
            }

            function renderMap(users) {
                const activeIds = [];
                const bounds = [];

                users.forEach(function (user) {
                    if (!(user.is_sharing && user.latitude !== null && user.longitude !== null)) {
                        return;
                    }

                    activeIds.push(user.id);
                    const latlng = [Number(user.latitude), Number(user.longitude)];
                    bounds.push(latlng);

                    const popup = '<strong>' + user.name + '</strong><br>' +
                        'Correo: ' + user.email + '<br>' +
                        'Ultima actualizacion: ' + formatDate(user.last_seen_at);

                    if (!markers[user.id]) {
                        markers[user.id] = L.marker(latlng, { icon: supportIcon }).addTo(map);
                    } else {
                        markers[user.id].setLatLng(latlng);
                    }

                    markers[user.id].bindPopup(popup);
                    markers[user.id].bindTooltip(user.name, { direction: 'top', offset: [0, -10] });
                    markers[user.id].off('click');
                    markers[user.id].on('click', function () {
                        loadHistory(user.id, user.name);
                    });
                });

                clearRemovedMarkers(activeIds);

                if (!hasAdminCenter && bounds.length) {
                    map.fitBounds(bounds, { padding: [30, 30], maxZoom: 17 });
                }
            }

            function loadData() {
                const officeId = officeFilter.value;
                const dataUrl = new URL('{{ route('admin.support.locations.data') }}', window.location.origin);
                if (officeId) {
                    dataUrl.searchParams.set('office_id', officeId);
                }

                fetch(dataUrl.toString(), {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('Error cargando ubicaciones.');
                    }

                    return response.json();
                })
                .then(function (payload) {
                    const users = Array.isArray(payload.users) ? payload.users : [];
                    renderTable(users);
                    renderMap(users);

                    if (!users.length) {
                        historyTitle.textContent = 'Historial reciente del tecnico seleccionado';
                        historyBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted p-3">No hay usuarios para mostrar historial.</td></tr>';
                        clearHistoryLine();
                    }
                })
                .catch(function () {
                    usersBody.innerHTML = '<tr><td colspan="4" class="text-center text-danger p-3">No se pudieron cargar las ubicaciones.</td></tr>';
                });
            }

            officeFilter.addEventListener('change', function () {
                loadData();
            });

            centerToAdminLocation();
            loadData();
            setInterval(loadData, 15000);
        })();
    </script>
@endsection
