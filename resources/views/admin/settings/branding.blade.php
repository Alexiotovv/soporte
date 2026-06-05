@extends('layouts.app')

@section('content')
<div class="container">
    @php
        $activeTab = request('tab');

        if (!$activeTab) {
            if ($errors->has('navbar_logo') || $errors->has('login_image')) {
                $activeTab = 'visual';
            } else {
                $activeTab = 'tickets';
            }
        }
    @endphp

    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Configuracion</div>

                <div class="card-body">
                    <ul class="nav nav-tabs mb-4" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link {{ $activeTab === 'tickets' ? 'active' : '' }}"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-tickets"
                                type="button"
                                role="tab"
                            >
                                Tickets
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link {{ $activeTab === 'visual' ? 'active' : '' }}"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-visual"
                                type="button"
                                role="tab"
                            >
                                Configuracion Visual
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade {{ $activeTab === 'tickets' ? 'show active' : '' }}" id="tab-tickets" role="tabpanel">
                            <p class="text-muted mb-4">
                                Registra categorias para clasificar tickets y asigna un color representativo.
                            </p>

                            <form action="{{ route('admin.settings.ticket-categories.store') }}" method="POST" class="border rounded p-3 mb-4 bg-light">
                                @csrf

                                <div class="row g-3 align-items-end">
                                    <div class="col-md-7">
                                        <label for="category_name" class="form-label fw-semibold">Nombre de categoria</label>
                                        <input
                                            type="text"
                                            class="form-control @error('name') is-invalid @enderror"
                                            id="category_name"
                                            name="name"
                                            value="{{ old('name') }}"
                                            placeholder="Ej: Redes, Hardware, Sistema"
                                            required
                                        >
                                        @error('name')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="category_color" class="form-label fw-semibold">Color</label>
                                        <input
                                            type="color"
                                            class="form-control form-control-color @error('color') is-invalid @enderror"
                                            id="category_color"
                                            name="color"
                                            value="{{ old('color', '#0d6efd') }}"
                                            title="Seleccionar color"
                                            required
                                        >
                                        @error('color')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-md-2 d-grid">
                                        <button type="submit" class="btn btn-primary">Agregar</button>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Categoria</th>
                                            <th>Color</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($ticketCategories as $category)
                                            <tr>
                                                <td>{{ $category->name }}</td>
                                                <td>
                                                    <span
                                                        class="badge"
                                                        style="background-color: {{ $category->color }}; color: #fff;"
                                                    >
                                                        {{ $category->color }}
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <form
                                                        action="{{ route('admin.settings.ticket-categories.destroy', $category) }}"
                                                        method="POST"
                                                        class="d-inline"
                                                        onsubmit="return confirm('Se eliminara esta categoria. Deseas continuar?');"
                                                    >
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-light">Eliminar</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-muted text-center">No hay categorias registradas.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade {{ $activeTab === 'visual' ? 'show active' : '' }}" id="tab-visual" role="tabpanel">
                            <p class="text-muted mb-4">
                                Desde aqui puedes subir una imagen para el login y un logo para la parte izquierda del navbar.
                            </p>

                            <form action="{{ route('admin.settings.branding.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="mb-4">
                                    <label for="navbar_logo" class="form-label fw-semibold">Logo del Navbar (superior izquierda)</label>
                                    <input
                                        type="file"
                                        class="form-control @error('navbar_logo') is-invalid @enderror"
                                        id="navbar_logo"
                                        name="navbar_logo"
                                        accept="image/*"
                                    >
                                    <div class="form-text">Formatos: JPG, PNG, WEBP o SVG. Recomendado: 40x40 px.</div>
                                    @error('navbar_logo')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror

                                    @if($brandingSetting->navbar_logo_url)
                                        <div class="mt-3">
                                            <span class="d-block text-muted mb-2">Vista previa actual:</span>
                                            <img
                                                src="{{ $brandingSetting->navbar_logo_url }}"
                                                alt="Logo actual del navbar"
                                                style="max-height: 48px; max-width: 180px; object-fit: contain;"
                                            >
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" value="1" id="remove_navbar_logo" name="remove_navbar_logo">
                                                <label class="form-check-label" for="remove_navbar_logo">
                                                    Quitar logo actual del navbar
                                                </label>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="mb-4">
                                    <label for="login_image" class="form-label fw-semibold">Imagen de Login</label>
                                    <input
                                        type="file"
                                        class="form-control @error('login_image') is-invalid @enderror"
                                        id="login_image"
                                        name="login_image"
                                        accept="image/*"
                                    >
                                    <div class="form-text">Formatos: JPG, PNG, WEBP o SVG. Recomendado: 1200x800 px.</div>
                                    @error('login_image')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror

                                    @if($brandingSetting->login_image_url)
                                        <div class="mt-3">
                                            <span class="d-block text-muted mb-2">Vista previa actual:</span>
                                            <img
                                                src="{{ $brandingSetting->login_image_url }}"
                                                alt="Imagen actual del login"
                                                class="img-fluid rounded border"
                                                style="max-height: 260px; object-fit: cover;"
                                            >
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" value="1" id="remove_login_image" name="remove_login_image">
                                                <label class="form-check-label" for="remove_login_image">
                                                    Quitar imagen actual del login
                                                </label>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">Guardar Configuracion</button>
                                    <a href="{{ url('/dashboard') }}" class="btn btn-light">Volver</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
