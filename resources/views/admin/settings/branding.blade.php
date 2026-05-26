@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">Configuracion Visual</div>

                <div class="card-body">
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
@endsection
