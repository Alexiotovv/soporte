@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Editar Usuario #{{ $user->id }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="name">Nombre</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Correo</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>


                        <div class="form-group">
                            <label for="phone">Teléfono</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                            @error('phone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="office_id">Oficina</label>
                            <select class="form-control @error('office_id') is-invalid @enderror" 
                                    id="office_id" name="office_id">
                                <option value="">Selecciona Oficina</option>
                                @foreach($offices as $office)
                                    <option value="{{ $office->id }}" 
                                        {{ (old('office_id', $user->office_id) == $office->id) ? 'selected' : '' }}>
                                        {{ $office->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('office_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>



                        <div class="form-group">
                            <label for="password">Contraseña (dejar en blanco para mantener el actual)</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password-confirm">Confirmar Contraseña</label>
                            <input type="password" class="form-control" id="password-confirm" name="password_confirmation">
                        </div>

                        <div class="form-group">
                            <label for="role">User Role</label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                <option value="0" {{ $user->is_admin == 0 ? 'selected' : '' }}>Regular User</option>
                                <option value="2" {{ $user->is_admin == 2 ? 'selected' : '' }}>Support Team</option>
                                <option value="1" {{ $user->is_admin == 1 ? 'selected' : '' }}>Administrator</option>
                            </select>
                            @error('role')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="role">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="0" {{ $user->status == 0 ? 'selected' : '' }}>Inhabilitado</option>
                                <option value="1" {{ $user->status == 1 ? 'selected' : '' }}>Activo</option>
                            </select>
                            @error('status')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
      
                        <br>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light btn-sm">⬅️ Regresar a lista </a>
                        <button type="submit" class="btn btn-light btn-sm">🔄 Actualizar Usuario</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection