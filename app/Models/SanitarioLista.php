<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SanitarioLista extends Model
{
    protected $table = 'sanitario_listas';

    public $timestamps = false; // Table doesn't have timestamps based on migration

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(SanitarioItem::class, 'lista_id');
    }

    public function respuestas(): HasMany
    {
        return $this->hasMany(SanitarioRespuesta::class, 'lista_id');
    }
}
