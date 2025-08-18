@component('mail::message')
# Solicitud de restablecimiento de contraseña

Hemos recibido una solicitud para restablecer la contraseña de tu cuenta.

@component('mail::button', ['url' => $url])
Restablecer Contraseña
@endcomponent

Este enlace de restablecimiento de contraseña expirará en {{ $count }} minutos.

Si no solicitaste un restablecimiento de contraseña, no es necesario realizar ninguna acción.

Gracias,<br>
{{ config('app.name') }}
@endcomponent