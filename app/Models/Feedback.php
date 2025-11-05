<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;
    
    protected $table = 'feedbacks';
    
    protected $fillable = [
        'cliente_id',
        'pedido_id',
        'tipo',
        'calificacion',
        'calificacion_comida',
        'calificacion_servicio',
        'calificacion_ambiente',
        'calificacion_precio',
        'calificacion_limpieza',
        'calificacion_web',
        'comentario',
        'sugerencias',
        'quejas',
        'elogios',
        'recomendaria',
        'frecuencia_visita',
        'estado',
        'respuesta_admin',
        'respondido_por',
        'respondido_at',
    ];

    protected $casts = [
        'calificacion' => 'integer',
        'calificacion_comida' => 'integer',
        'calificacion_servicio' => 'integer',
        'calificacion_ambiente' => 'integer',
        'calificacion_precio' => 'integer',
        'calificacion_limpieza' => 'integer',
        'calificacion_web' => 'integer',
        'recomendaria' => 'boolean',
        'respondido_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con el cliente (Usuario)
     */
    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    /**
     * Relación con el pedido (opcional)
     */
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    /**
     * Relación con el administrador que respondió
     */
    public function administrador()
    {
        return $this->belongsTo(User::class, 'respondido_por');
    }

    /**
     * Scope para filtrar por tipo
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope para filtrar por estado
     */
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope para filtrar por calificación
     */
    public function scopeConCalificacion($query, $calificacion)
    {
        return $query->where('calificacion', $calificacion);
    }

    /**
     * Scope para feedbacks recientes
     */
    public function scopeRecientes($query, $dias = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($dias));
    }

    /**
     * Scope para feedbacks pendientes de respuesta
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    /**
     * Calcular calificación general promedio
     */
    public function getCalificacionGeneralAttribute()
    {
        $calificaciones = array_filter([
            $this->calificacion_comida,
            $this->calificacion_servicio,
            $this->calificacion_ambiente,
            $this->calificacion_precio,
            $this->calificacion_limpieza,
            $this->calificacion_web,
        ]);

        if (empty($calificaciones)) {
            return $this->calificacion;
        }

        return round(array_sum($calificaciones) / count($calificaciones), 2);
    }

    /**
     * Accessor para obtener emoji según calificación
     */
    public function getEmojiAttribute()
    {
        $calificacion = $this->calificacion_general ?? $this->calificacion;
        
        return match(true) {
            $calificacion >= 4.5 => '😍',  // Excelente
            $calificacion >= 3.5 => '😊',  // Muy bueno
            $calificacion >= 2.5 => '😐',  // Normal
            $calificacion >= 1.5 => '😕',  // Regular
            default => '😡'                 // Malo
        };
    }

    /**
     * Accessor para obtener texto de calificación
     */
    public function getTextoCalificacionAttribute()
    {
        $calificacion = $this->calificacion_general ?? $this->calificacion;
        
        return match(true) {
            $calificacion >= 4.5 => 'Excelente',
            $calificacion >= 3.5 => 'Muy bueno',
            $calificacion >= 2.5 => 'Bueno',
            $calificacion >= 1.5 => 'Regular',
            default => 'Malo'
        };
    }

    /**
     * Accessor para color de badge
     */
    public function getColorBadgeAttribute()
    {
        $calificacion = $this->calificacion_general ?? $this->calificacion;
        
        return match(true) {
            $calificacion >= 4.5 => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
            $calificacion >= 3.5 => 'bg-green-500/20 text-green-400 border-green-500/30',
            $calificacion >= 2.5 => 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',
            $calificacion >= 1.5 => 'bg-orange-500/20 text-orange-400 border-orange-500/30',
            default => 'bg-red-500/20 text-red-400 border-red-500/30'
        };
    }

    /**
     * Accessor para icono según tipo
     */
    public function getTipoIconoAttribute()
    {
        return match($this->tipo) {
            'general' => '💬',
            'pedido' => '🛒',
            'servicio' => '👨‍🍳',
            'local' => '🏪',
            'web' => '🌐',
            default => '📝'
        };
    }

    /**
     * Accessor para texto del tipo
     */
    public function getTipoTextoAttribute()
    {
        return match($this->tipo) {
            'general' => 'General',
            'pedido' => 'Pedido Específico',
            'servicio' => 'Servicio al Cliente',
            'local' => 'Instalaciones',
            'web' => 'Sitio Web / App',
            default => 'Otro'
        };
    }

    /**
     * Accessor para estado texto
     */
    public function getEstadoTextoAttribute()
    {
        return match($this->estado) {
            'pendiente' => 'Pendiente de revisión',
            'revisado' => 'Revisado',
            'respondido' => 'Respondido',
            'resuelto' => 'Resuelto',
            default => 'Sin estado'
        };
    }

    /**
     * Accessor para color del estado
     */
    public function getEstadoColorAttribute()
    {
        return match($this->estado) {
            'pendiente' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',
            'revisado' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
            'respondido' => 'bg-purple-500/20 text-purple-400 border-purple-500/30',
            'resuelto' => 'bg-green-500/20 text-green-400 border-green-500/30',
            default => 'bg-zinc-500/20 text-zinc-400 border-zinc-500/30'
        };
    }
}
