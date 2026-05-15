<?php

return [
    'attributes' => [
        'email' => 'Correo Electronico',
        'password' => 'Contrasena',
        'login' => 'Iniciar Sesion',
        'rememberme' => 'Recordar credenciales',
    ],

    // Mensajes de validacion estandar
    'accepted' => 'El campo :attribute debe ser aceptado.',
    'required' => 'El campo :attribute es obligatorio.',

    'custom' => [
        'password' => [
            'regex' => 'La contrasena debe tener al menos una letra, un numero y un simbolo.',
        ],
    ],
    'min' => [
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'file'    => 'El archivo :attribute debe tener al menos :min kilobytes.',
        'string'  => 'El campo :attribute debe tener al menos :min caracteres.',
        'array'   => 'El campo :attribute debe tener al menos :min elementos.',
    ],

];
