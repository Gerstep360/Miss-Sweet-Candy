<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SanitarioItem extends Model
{
    protected $table = 'sanitario_items';
    public $timestamps = false;

    protected $fillable = [
        'lista_id',
        'texto',
        'tipo',
    ];

    public function lista(): BelongsTo
    {
        return $this->belongsTo(SanitarioLista::class, 'lista_id');
    }

    public function respuestas(): HasMany
    {
        return $this->hasMany(SanitarioRespuesta::class, 'item_id');
    }
}
