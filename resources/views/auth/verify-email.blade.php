@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <div class="card shadow-sm">
                <div class="card-header ">
                    Verifica tu correo electrónico
                </div>
                <div class="card-body">
                    
                    <p>
                        Antes de continuar, confirma tu dirección de correo haciendo clic en el enlace que te enviamos. 
                        Si no recibiste el correo, puedes solicitar otro.
                    </p>

                    @if (session('message'))
                        <div class="alert alert-success" role="alert">
                            {{ session('message') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary btn-sm">
                            Reenviar enlace de verificación
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
