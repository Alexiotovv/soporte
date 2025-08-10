@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Crear Nueva Oficina</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.offices.store') }}">
                        @csrf

                        <div class="form-group">
                            <label for="name">Nombre de la Oficina</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <br>
                        <button type="submit" class="btn btn-light btn-sm">➕ Guardar Oficina</button>
                        <a href="{{ route('admin.offices.index') }}" class="btn btn-light btn-sm">⬅️ Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection