<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User; // 👈 importa el User

class Auditoria extends Model
{
    public $timestamps = false;

    protected $table = 'auditorias';

    protected $fillable = [
        'usuario_id',
        'accion',
        'entidad',
        'entidad_id',
        'ip',
        'user_agent',
        'created_at',
    ];

    // 👇 Usa $casts (no $dates) para que created_at sea Carbon
    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // (Opcional) Accesor práctico para formatear desde la vista
    public function getCreatedAtFormattedAttribute(): ?string
    {
        return $this->created_at
            ? $this->created_at->format('d/m/Y H:i:s')
            : null;
    }

    /**
     * Scope para filtrar por acción
     */
    public function scopePorAccion($query, $accion)
    {
        return $query->where('accion', $accion);
    }

    /**
     * Scope para filtrar por entidad
     */
    public function scopePorEntidad($query, $entidad)
    {
        return $query->where('entidad', $entidad);
    }

    /**
     * Scope para filtrar por usuario
     */
    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    /**
     * Scope para filtrar por IP
     */
    public function scopePorIp($query, $ip)
    {
        return $query->where('ip', $ip);
    }

    /**
     * Scope para filtrar por rango de fechas
     */
    public function scopePorRangoFechas($query, $desde, $hasta)
    {
        return $query->whereBetween('created_at', [$desde, $hasta]);
    }

    /**
     * Scope para acciones de creación
     */
    public function scopeCreaciones($query)
    {
        return $query->whereIn('accion', ['crear', 'create', 'store']);
    }

    /**
     * Scope para acciones de edición
     */
    public function scopeEdiciones($query)
    {
        return $query->whereIn('accion', ['editar', 'edit', 'update', 'actualizar', 'modificar']);
    }

    /**
     * Scope para acciones de eliminación
     */
    public function scopeEliminaciones($query)
    {
        return $query->whereIn('accion', ['eliminar', 'delete', 'destroy', 'eliminado']);
    }
}
