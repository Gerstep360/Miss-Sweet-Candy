<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SanitarioRespuesta extends Model
{
    protected $table = 'sanitario_respuestas';
    public $timestamps = true;

    protected $fillable = [
        'lista_id',
        'item_id',
        'usuario_id',
        'valor_texto',
        'valor_numero',
        'valor_check',
        'foto_ruta',
    ];

    protected $casts = [
        'valor_check' => 'boolean',
        'valor_numero' => 'decimal:2',
    ];

    public function lista(): BelongsTo
    {
        return $this->belongsTo(SanitarioLista::class, 'lista_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(SanitarioItem::class, 'item_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
