<?php
// app/Support/BusinessHours.php
namespace App\Support;

use App\Models\Horario;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class BusinessHours
{
    /** Mapa español -> índice Carbon (0=Dom, 6=Sáb) */
    private array $dayMap = [
        'domingo' => 0, 'lunes' => 1, 'martes' => 2, 'miércoles' => 3,
        'jueves' => 4, 'viernes' => 5, 'sábado' => 6,
    ];

    /** Retorna [start,end] (Carbon) para el día $now */
    public function rangeFor(Carbon $now): ?array
    {
        $all = Cache::remember('horarios:all', 3600, function () {
            return Horario::all()->mapWithKeys(function ($h) {
                return [mb_strtolower($h->dia) => ['abre' => $h->abre, 'cierra' => $h->cierra]];
            })->toArray();
        });

        $key = array_search($now->dayOfWeek, $this->dayMap, true);
        // array_search devuelve índice; queremos nombre. Mejor recorremos:
        $nombre = array_search($now->dayOfWeek, $this->dayMap) ?: null;

        if (!$nombre || !isset($all[$nombre])) {
            return null; // cerrado
        }
        $start = $now->copy()->setTimeFromTimeString($all[$nombre]['abre']);
        $end   = $now->copy()->setTimeFromTimeString($all[$nombre]['cierra']);
        return [$start, $end];
    }

    public function isOpenAt(Carbon $now): bool
    {
        [$start, $end] = $this->rangeFor($now) ?? [null, null];
        return $start && $now->between($start, $end);
    }

    /** Segundos hasta abrir/cerrar (para TTL de caché) */
    public function secondsUntilNextTransition(Carbon $now): int
    {
        [$start, $end] = $this->rangeFor($now) ?? [null, null];
        if ($start && $end) {
            if ($this->isOpenAt($now)) {
                return max(60, $now->diffInSeconds($end, false));
            }
            if ($now->lt($start)) {
                return max(60, $now->diffInSeconds($start, false));
            }
        }
        // Buscar próxima apertura (mañana o siguiente con horario)
        for ($i = 1; $i <= 7; $i++) {
            $d = $now->copy()->addDays($i);
            if ($r = $this->rangeFor($d)) {
                $open = $d->copy()->setTimeFromTimeString($r[0]->format('H:i'));
                return max(60, $now->diffInSeconds($open, false));
            }
        }
        return 300; // fallback
    }
}
