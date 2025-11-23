<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Programar verificación de promociones próximas a expirar (todos los días a las 9:00 AM)
Schedule::command('promociones:verificar-expiracion')
    ->dailyAt('09:00')
    ->timezone('America/La_Paz');
