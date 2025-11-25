<?php

use App\Models\Promocion;
use App\Models\User;
use App\Mail\PromocionCreada;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Iniciando prueba de envío de email...\n";

// 1. Obtener una promoción existente o crear una dummy
$promocion = Promocion::first();

if (!$promocion) {
    echo "No hay promociones. Creando una dummy...\n";
    $promocion = new Promocion([
        'nombre' => 'Promoción de Prueba',
        'tipo' => 'porcentaje',
        'valor' => 10,
        'activo' => true
    ]);
}

echo "Usando promoción: {$promocion->nombre}\n";

// 2. Obtener un usuario verificado
$usuario = User::whereNotNull('email_verified_at')->first();

if (!$usuario) {
    echo "Error: No hay usuarios verificados.\n";
    exit(1);
}

echo "Enviando email a: {$usuario->email}\n";

try {
    Mail::to($usuario->email)->send(new PromocionCreada($promocion));
    echo "✅ Email enviado correctamente (o encolado).\n";
} catch (\Exception $e) {
    echo "❌ Error al enviar email: " . $e->getMessage() . "\n";
    Log::error("Error prueba email: " . $e->getMessage());
}

echo "Revisa storage/logs/laravel.log para más detalles si usas el driver 'log'.\n";
