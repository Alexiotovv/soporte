<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('ticket.{ticketId}', function ($user, $ticketId) {
    // Aquí puedes poner tu validación real
    // por ejemplo, si el usuario pertenece al ticket
    return true; // Temporalmente abierto para pruebas
});
