<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('turnero.{token}', function ($user, $token) {
    // lógica de autorización, por ejemplo permitir si pertenece al pedido
    return true;
});
