<?php

use Illuminate\Support\Facades\Route;

// Secciones Web
require __DIR__.'/public.php';
require __DIR__.'/dashboard.php';
require __DIR__.'/settings.php';
require __DIR__.'/admin.php';

// Cajero (separado por áreas)
require __DIR__.'/cajero/mesas.php';
require __DIR__.'/cajero/pedidos.php';
require __DIR__.'/cajero/cobro_caja.php';

// API
require __DIR__.'/api.php';


require __DIR__.'/auth.php';
require __DIR__.'/error/403.php';
require __DIR__.'/bitacora/bitacora.php';
require __DIR__.'/barista/barista.php';
