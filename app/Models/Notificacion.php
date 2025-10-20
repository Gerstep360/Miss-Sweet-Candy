<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    use HasFactory;

    protected $table = 'notificaciones';

    // Desactivar timestamps ya que la tabla no los tiene
    public $timestamps = false;

    protected $fillable = [
        'tipo',
        'canal',
        'mensaje',
        'usuario_destino_id',
        'rel_model',
        'rel_id',
        'leido',
    ];

    protected $casts = [
        'leido' => 'boolean',
    ];

    // Relación con el usuario destinatario
    public function usuarioDestino()
    {
        return $this->belongsTo(User::class, 'usuario_destino_id');
    }

    // Scope para obtener solo notificaciones no leídas
    public function scopeNoLeidas($query)
    {
        return $query->where('leido', false);
    }

    // Scope para obtener notificaciones de un usuario específico
    public function scopeParaUsuario($query, $usuarioId)
    {
        return $query->where('usuario_destino_id', $usuarioId);
    }

    // Marcar como leída
    public function marcarComoLeida()
    {
        $this->update(['leido' => true]);
    }
}
