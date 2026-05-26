@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card mb-3">
                <div class="card-header">Mi Ubicacion de Soporte</div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Solo usuarios de soporte pueden habilitar la geolocalizacion. Al activarla, el administrador podra ver tu ubicacion en el mapa.
                    </p>

                    <form method="POST" action="{{ route('support.location.sharing') }}" class="d-flex flex-wrap align-items-center gap-2 mb-3">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="is_sharing" value="0">
                        <div class="form-check form-switch m-0">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                role="switch"
                                id="is_sharing"
                                name="is_sharing"
                                value="1"
                                {{ $location->is_sharing ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="is_sharing">Habilitar coordenadas en el sitio</label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Guardar estado</button>
                    </form>

                    <div class="mb-3">
                        @if($location->is_sharing)
                            <span class="badge bg-success">Ubicacion activa</span>
                        @else
                            <span class="badge bg-secondary">Ubicacion desactivada</span>
                        @endif
                        <span class="ms-2 text-muted" id="status-text"></span>
                    </div>

                    <div id="support-map" style="height: 420px; border-radius: 12px; overflow: hidden;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        .my-support-marker {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #1e6b52;
            border: 3px solid #ffffff;
            box-shadow: 0 0 0 3px rgba(30, 107, 82, 0.26);
        }
    </style>
@endsection

@section('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        (function () {
            const sharingEnabled = {{ $location->is_sharing ? 'true' : 'false' }};
            const statusText = document.getElementById('status-text');
            const myIcon = L.divIcon({
                className: 'my-support-marker-wrap',
                html: '<div class="my-support-marker"></div>',
                iconSize: [26, 26],
                iconAnchor: [13, 13],
            });

            const map = L.map('support-map').setView([-12.0464, -77.0428], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            let marker = null;
            let watchId = null;
            let lastSentAt = 0;

            function setStatus(message) {
                statusText.textContent = message;
            }

            function updateMarker(lat, lng) {
                if (!marker) {
                    marker = L.marker([lat, lng], { icon: myIcon }).addTo(map);
                } else {
                    marker.setLatLng([lat, lng]);
                }

                marker.bindPopup('Tu ubicacion actual').openPopup();
                map.setView([lat, lng], 17);
            }

            function sendCoordinates(lat, lng) {
                fetch('{{ route('support.location.update') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        latitude: lat,
                        longitude: lng
                    })
                })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('No se pudo actualizar coordenadas.');
                    }

                    return response.json();
                })
                .then(function () {
                    setStatus('Coordenadas enviadas correctamente.');
                })
                .catch(function (error) {
                    setStatus(error.message || 'Error al enviar coordenadas.');
                });
            }

            function onPosition(position) {
                const lat = Number(position.coords.latitude.toFixed(7));
                const lng = Number(position.coords.longitude.toFixed(7));
                updateMarker(lat, lng);

                const now = Date.now();
                if (now - lastSentAt >= 15000) {
                    lastSentAt = now;
                    sendCoordinates(lat, lng);
                }
            }

            function onPositionError(error) {
                setStatus('No fue posible obtener la ubicacion: ' + error.message);
            }

            function startTracking() {
                if (!navigator.geolocation) {
                    setStatus('Este navegador no soporta geolocalizacion.');
                    return;
                }

                setStatus('Buscando ubicacion...');
                watchId = navigator.geolocation.watchPosition(onPosition, onPositionError, {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 5000
                });
            }

            if (sharingEnabled) {
                startTracking();
            } else {
                setStatus('Activa el interruptor para compartir tu ubicacion.');
            }

            window.addEventListener('beforeunload', function () {
                if (watchId !== null) {
                    navigator.geolocation.clearWatch(watchId);
                }
            });
        })();
    </script>
@endsection
