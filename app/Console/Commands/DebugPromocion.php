<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Promocion;

class DebugPromocion extends Command
{
    protected $signature = 'debug:promocion {id?}';
    protected $description = 'Debug de una promoción específica';

    public function handle()
    {
        $id = $this->argument('id') ?? 1;
        $p = Promocion::find($id);
        
        if (!$p) {
            $this->error("No se encontró la promoción con ID {$id}");
            return 1;
        }

        $this->info('===== DEBUG PROMOCIÓN =====');
        $this->line('Nombre: ' . $p->nombre);
        $this->line('Activo: ' . ($p->activo ? 'SI' : 'NO'));
        $this->line('Hora inicio (DB): ' . ($p->getAttributes()['hora_inicio'] ?? 'null'));
        $this->line('Hora fin (DB): ' . ($p->getAttributes()['hora_fin'] ?? 'null'));
        $this->line('Hora actual: ' . now()->format('H:i:s'));
        $this->line('Fecha actual: ' . now()->toDateString());
        $this->line('Fecha inicio: ' . ($p->fecha_inicio ? $p->fecha_inicio->toDateString() : 'null'));
        $this->line('Fecha fin: ' . ($p->fecha_fin ? $p->fecha_fin->toDateString() : 'null'));
        $this->line('Días semana: ' . json_encode($p->dias_semana));
        $this->line('Día actual: ' . now()->format('D'));
        
        $this->info('');
        $this->info('RESULTADO: Esta vigente = ' . ($p->esta_vigente ? 'SI ✓' : 'NO ✗'));
        
        return 0;
    }
}
