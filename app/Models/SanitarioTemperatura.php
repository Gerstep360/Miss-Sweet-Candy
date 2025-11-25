<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SanitarioTemperatura extends Model
{
    protected $table = 'sanitario_temperaturas';
    public $timestamps = true;

    protected $fillable = [
        'equipo',
        'temperatura',
        'usuario_id',
    ];

    protected $casts = [
        'temperatura' => 'decimal:2',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
