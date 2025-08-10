@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Editar Oficina #{{ $office->id }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.offices.update', $office->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="name">Nombre de la Oficina</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $office->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <br>
                        <button type="submit" class="btn btn-light btn-sm">🔄 Actualizar Oficina</button>
                        <a href="{{ route('admin.offices.index') }}" class="btn btn-light btn-sm">⬅️ Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection