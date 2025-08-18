<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Email' }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .button { background: #3490dc; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    {{-- Logo --}}
    <div style="text-align: center; margin-bottom: 20px;">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 50px;">
    </div>

    {{-- Contenido del email --}}
    {{ $slot }}

    {{-- Footer --}}
    <div style="margin-top: 30px; text-align: center; font-size: 0.8em; color: #666;">
        © {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.
    </div>
</body>
</html>