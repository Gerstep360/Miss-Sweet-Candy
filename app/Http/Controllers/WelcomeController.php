<?php

namespace App\Http\Controllers;

use App\Models\EspecialDelDia;
use App\Models\Horario;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index(Request $request)
    {
        // TZ/locale
        $tz = config('app.timezone', 'America/La_Paz');
        Carbon::setLocale(app()->getLocale() ?? 'es');
        $ahora = Carbon::now($tz);

        // Horarios (directo a BD, SIN caché)
        $horarios = Horario::orderByRaw("FIELD(dia,'lunes','martes','miercoles','jueves','viernes','sabado','domingo')")
            ->get(['dia','abre','cierra']);

        // Estado abierto/cerrado + rango de HOY (maneja cruce de medianoche)
        $estadoLocal = $this->estadoLocal($horarios, $ahora);

        // Especial del día (directo a BD, SIN caché)
        $especial = EspecialDelDia::getEspecialHoy();
        $especialHoy = $this->mapEspecial($especial);

        // Destacados
        $destacados = Producto::query()
            ->with(['especialVigente','categoria']) // 👈 evita N+1
            ->latest('created_at')
            ->take(6)
            ->get(['id','nombre','precio','imagen'])
            ->each->append([
                'imagen_url',
                'precio_vigente',
                'tiene_oferta',
                'ahorro_vigente',
                'porcentaje_oferta',
            ]);

        // Datos “de JS” ahora precocinados en servidor
        $ui = [
            // Iframe listo (con lazy), sin IntersectionObserver
            'map_iframe_src' => "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3792.8!2d-63.1631317!3d-17.7433968!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTfCsDQ0JzM2LjIiUyA2M8KwMDknNDcuMyJX!5e0!3m2!1ses!2sbo!4v1640995200000!5m2!1ses!2sbo",
            'map_dir_href'   => "https://www.google.com/maps/dir//-17.7433967590332,-63.1631317138672",
            // Copiamos la estética del header via clases; efectos on-scroll ya no dependen de JS
            'header_classes' => 'backdrop-blur bg-zinc-950/80',
        ];

        $meta = [
            'title'       => 'Miss Sweet Candy - La Mejor Experiencia de Café',
            'description' => 'Café artesanal, repostería y el mejor ambiente. Descubre el Especial del Día y visítanos.',
        ];

        return view('welcome', compact('horarios','estadoLocal','especialHoy','destacados','ui','meta'));
    }

    public function especialHoyJson()
    {
        $e = EspecialDelDia::getEspecialHoy();
        return response()->json(['success' => (bool) $e, 'data' => $this->mapEspecial($e)]);
    }

    public function estadoJson()
    {
        $tz = config('app.timezone', 'America/La_Paz');
        $ahora = Carbon::now($tz);
        $horarios = Horario::orderByRaw("FIELD(dia,'lunes','martes','miercoles','jueves','viernes','sabado','domingo')")
            ->get(['dia','abre','cierra']);

        return response()->json(['success' => true, 'data' => $this->estadoLocal($horarios, $ahora)]);
    }

    /* ===================== Helpers ===================== */

    private function estadoLocal($horarios, Carbon $ahora): array
    {
        $dia = strtolower($ahora->locale('es')->dayName); // ej. "martes"
        $hoy = $horarios->firstWhere('dia', $dia);

        if (!$hoy) {
            return ['abierto' => false, 'texto' => 'Sin horario para hoy', 'rango' => null];
        }

        // Construir hora de apertura/cierre para HOY; si cierra <= abre => cierra al día siguiente
        $abre  = Carbon::createFromFormat('H:i:s', $hoy->abre, $ahora->timezone)->setDate($ahora->year, $ahora->month, $ahora->day);
        $cierra= Carbon::createFromFormat('H:i:s', $hoy->cierra, $ahora->timezone)->setDate($ahora->year, $ahora->month, $ahora->day);
        if ($cierra->lessThanOrEqualTo($abre)) {
            $cierra->addDay(); // horario de noche que cruza medianoche
        }

        $abierto = $ahora->betweenIncluded($abre, $cierra);

        return [
            'abierto' => $abierto,
            'texto'   => $abierto ? '¡Abierto!' : 'Cerrado',
            'rango'   => sprintf('%s - %s', $abre->format('g:i A'), $cierra->format('g:i A')),
        ];
    }

    private function mapEspecial(?EspecialDelDia $e): array
    {
        $e?->loadMissing('producto.categoria');
        if (!$e || !$e->producto) {
            return ['existe' => false];
        }

        $precioOriginal = (float) $e->producto->precio;
        $precioFinal    = (float) $e->getPrecioFinal();
        $ahorro         = max(0, $precioOriginal - $precioFinal);
        $porcentaje     = $precioOriginal > 0 ? (int) round(($ahorro / $precioOriginal) * 100) : 0;

        return [
            'existe'           => true,
            'id'               => $e->id,
            'producto_id'      => $e->producto->id,
            'nombre'           => $e->producto->nombre,
            'categoria'        => $e->producto->categoria->nombre ?? null,
            'imagen_url'       => $e->producto->imagen_url,
            'precio_original'  => number_format($precioOriginal, 2, '.', ''),
            'precio_final'     => number_format($precioFinal,   2, '.', ''),
            'ahorro'           => number_format($ahorro,        2, '.', ''),
            'porcentaje'       => $porcentaje,
            'tipo'             => !is_null($e->precio_especial) ? 'precio_fijo' : 'porcentaje',
            'descripcion'      => $e->getDescripcionCompleta(),
            'dia_semana'       => $e->dia_semana,
            'fecha_especifica' => $e->fecha_especifica?->format('Y-m-d'),
            'activo'           => (bool) $e->activo,
        ];
    }
}
