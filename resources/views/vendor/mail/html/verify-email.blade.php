@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
{{ config('app.name') }}
@endcomponent
@endslot

{{-- Body --}}
<h1>Verifica tu dirección de email</h1>
<p>Hola {{ $user->name }},</p>
<p>Por favor haz clic en el botón siguiente para verificar tu dirección de email:</p>

@component('mail::button', ['url' => $verificationUrl, 'color' => 'primary'])
Verificar Email
@endcomponent

<p>Si no creaste una cuenta, no es necesario realizar ninguna acción.</p>

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
© {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.
@endcomponent
@endslot
@endcomponent