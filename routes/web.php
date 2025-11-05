<?php

use Illuminate\Support\Facades\Route;

// Secciones Web
require __DIR__.'/welcome.php';
require __DIR__.'/public.php';
require __DIR__.'/dashboard.php';
require __DIR__.'/settings.php';
require __DIR__.'/admin.php';

// Cajero (separado por áreas)
require __DIR__.'/cajero/mesas.php';
require __DIR__.'/cajero/pedidos.php';
require __DIR__.'/cajero/cobro_caja.php';

// Cierres de caja
require __DIR__.'/cierres_caja.php';

// Turnos de caja
require __DIR__.'/turnos_caja.php';

// Fidelidad - NUEVO SISTEMA - AGREGAR ESTA LÍNEA
require __DIR__ . '/fidelidad.php';

// API
require __DIR__.'/api.php';

// Notificaciones
require __DIR__.'/notificaciones.php';

// Inventario
require __DIR__.'/inventario.php';

// Reportes
require __DIR__.'/reportes.php';

require __DIR__.'/auth.php';
//require __DIR__.'/error/error.php';
require __DIR__.'/Bitacora/bitacora.php';
require __DIR__.'/barista/barista.php';

require __DIR__.'/especial_dia.php';

// Feedback
require __DIR__.'/feedback.php';