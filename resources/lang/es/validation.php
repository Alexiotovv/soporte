<?php
return [
    'attributes' => [
        'email' => 'Correo Electrónico',
        'password' => 'Contraseña',
        'Login' => 'Iniciar Sesión',
        'rememberme' => 'Recordar credenciales',
        // Agrega más campos según necesites
    ],
    
    // Mensajes de validación estándar
    'accepted' => 'El campo :attribute debe ser aceptado.',
    'required' => 'El campo :attribute es obligatorio.',
    
    'custom' => [
        'password' => [
            'regex' => 'La contraseña debe tener al menos una letra, un número y un símbolo.',
        ],
    ],
    'min' => [
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'file'    => 'El archivo :attribute debe tener al menos :min kilobytes.',
        'string'  => 'El campo :attribute debe tener al menos :min caracteres.',
        'array'   => 'El campo :attribute debe tener al menos :min elementos.',
    ],

];