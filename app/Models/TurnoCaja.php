<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TurnoCaja extends Model
{
    public $timestamps = false;

    protected $table = 'turnos_caja';

    protected $fillable = [
        'cajero_id',
        'inicio',
        'fin',
        'estado',
        'monto_inicial',
        'observaciones_apertura',
        'observaciones_cierre'
    ];

    protected $casts = [
        'inicio' => 'datetime',
        'fin' => 'datetime',
        'monto_inicial' => 'decimal:2'
    ];

    // ============================================
    // RELACIONES
    // ============================================

    public function cajero()
    {
        return $this->belongsTo(User::class, 'cajero_id');
    }

    public function cierre()
    {
        return $this->hasOne(CierreCaja::class, 'turno_id');
    }

    // ============================================
    // MÉTODOS ESTÁTICOS (Turno Global)
    // ============================================

    /**
     * Obtener el turno activo actual (si existe)
     */
    public static function turnoActivo()
    {
        return self::where('estado', 'activo')
                   ->with('cajero')
                   ->first();
    }

    /**
     * Verificar si hay un turno activo
     */
    public static function hayTurnoActivo()
    {
        return self::where('estado', 'activo')->exists();
    }

    /**
     * Verificar si un cajero específico tiene turno activo
     */
    public static function tieneTurnoActivo($cajeroId = null)
    {
        $cajeroId = $cajeroId ?? Auth::id();
        
        return self::where('cajero_id', $cajeroId)
                   ->where('estado', 'activo')
                   ->exists();
    }

    /**
     * Iniciar un nuevo turno (validación incluida)
     */
    public static function iniciarTurno($cajeroId, $montoInicial = 0, $observaciones = null)
    {
        // Verificar si ya hay un turno activo
        if (self::hayTurnoActivo()) {
            $turnoActivo = self::turnoActivo();
            return [
                'success' => false,
                'message' => "{$turnoActivo->cajero->name} está de turno actualmente. Debe cerrar su turno primero.",
                'turno' => $turnoActivo
            ];
        }

        // Verificar si el cajero ya tiene un turno activo (validación adicional)
        if (self::tieneTurnoActivo($cajeroId)) {
            return [
                'success' => false,
                'message' => 'Ya tienes un turno activo. Debes cerrarlo antes de iniciar uno nuevo.'
            ];
        }

        // Crear nuevo turno
        $turno = self::create([
            'cajero_id' => $cajeroId,
            'inicio' => now(),
            'estado' => 'activo',
            'monto_inicial' => $montoInicial,
            'observaciones_apertura' => $observaciones
        ]);

        return [
            'success' => true,
            'message' => 'Turno iniciado correctamente',
            'turno' => $turno->load('cajero')
        ];
    }

    // ============================================
    // MÉTODOS DE INSTANCIA
    // ============================================

    /**
     * Cerrar este turno
     */
    public function cerrarTurno($observaciones = null)
    {
        if ($this->estado === 'cerrado') {
            return [
                'success' => false,
                'message' => 'Este turno ya está cerrado'
            ];
        }

        $this->update([
            'fin' => now(),
            'estado' => 'cerrado',
            'observaciones_cierre' => $observaciones
        ]);

        return [
            'success' => true,
            'message' => 'Turno cerrado correctamente'
        ];
    }

    /**
     * Verificar si este turno está activo
     */
    public function estaActivo()
    {
        return $this->estado === 'activo';
    }

    /**
     * Verificar si este turno está cerrado
     */
    public function estaCerrado()
    {
        return $this->estado === 'cerrado';
    }

    /**
     * Verificar si este turno es del cajero actual
     */
    public function esMiTurno()
    {
        return $this->cajero_id === Auth::id();
    }

    // ============================================
    // ACCESSORS
    // ============================================

    /**
     * Obtener duración del turno en horas
     */
    public function getDuracionAttribute()
    {
        if (!$this->fin) {
            return null;
        }

        return $this->inicio->diffInHours($this->fin);
    }

    /**
     * Obtener tiempo transcurrido (para turnos activos)
     */
    public function getTiempoTranscurridoAttribute()
    {
        if ($this->estado === 'cerrado') {
            return null;
        }

        return $this->inicio->diffForHumans();
    }

    /**
     * Obtener duración formateada
     */
    public function getDuracionFormateadaAttribute()
    {
        if (!$this->fin) {
            $diff = $this->inicio->diff(now());
            return sprintf('%dh %dm', $diff->h + ($diff->days * 24), $diff->i);
        }

        $diff = $this->inicio->diff($this->fin);
        return sprintf('%dh %dm', $diff->h + ($diff->days * 24), $diff->i);
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopeActivo($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeCerrado($query)
    {
        return $query->where('estado', 'cerrado');
    }

    public function scopeHoy($query)
    {
        return $query->whereDate('inicio', today());
    }

    public function scopeDeCajero($query, $cajeroId)
    {
        return $query->where('cajero_id', $cajeroId);
    }

    public function scopeEntreFechas($query, $inicio, $fin)
    {
        return $query->whereBetween('inicio', [$inicio, $fin]);
    }
}
