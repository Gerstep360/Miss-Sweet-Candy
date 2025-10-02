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
}
