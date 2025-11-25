<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Alergeno extends Model
{
    protected $table = 'alergenos';

    protected $fillable = [
        'nombre',
        'icono',
        'color',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Productos que contienen este alérgeno
     */
    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(Producto::class, 'alergeno_producto')
                    ->withPivot('nivel_presencia')
                    ->withTimestamps();
    }

    /**
     * Scope para obtener solo alérgenos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Obtener el badge HTML para mostrar el alérgeno
     */
    public function getBadgeAttribute(): string
    {
        $colores = [
            'red' => 'bg-red-500/20 text-red-300 border-red-500/50',
            'orange' => 'bg-orange-500/20 text-orange-300 border-orange-500/50',
            'yellow' => 'bg-yellow-500/20 text-yellow-300 border-yellow-500/50',
        ];

        $clase = $colores[$this->color] ?? $colores['red'];
        
        return "<span class='inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium border {$clase}'>
                    {$this->icono} {$this->nombre}
                </span>";
    }

    /**
     * Alérgenos comunes predefinidos
     */
    public static function alergenosComunes(): array
    {
        return [
            ['nombre' => 'Gluten', 'icono' => '🌾', 'color' => 'orange', 'descripcion' => 'Presente en trigo, cebada, centeno'],
            ['nombre' => 'Lácteos', 'icono' => '🥛', 'color' => 'orange', 'descripcion' => 'Leche y derivados lácteos'],
            ['nombre' => 'Huevo', 'icono' => '🥚', 'color' => 'yellow', 'descripcion' => 'Huevo y productos que lo contengan'],
            ['nombre' => 'Frutos Secos', 'icono' => '🥜', 'color' => 'red', 'descripcion' => 'Nueces, almendras, avellanas, etc.'],
            ['nombre' => 'Maní/Cacahuete', 'icono' => '🥜', 'color' => 'red', 'descripcion' => 'Cacahuete y derivados'],
            ['nombre' => 'Soja', 'icono' => '🫘', 'color' => 'orange', 'descripcion' => 'Soja y productos derivados'],
            ['nombre' => 'Pescado', 'icono' => '🐟', 'color' => 'orange', 'descripcion' => 'Pescado y productos pesqueros'],
            ['nombre' => 'Mariscos', 'icono' => '🦐', 'color' => 'red', 'descripcion' => 'Crustáceos y moluscos'],
            ['nombre' => 'Apio', 'icono' => '🥬', 'color' => 'yellow', 'descripcion' => 'Apio y derivados'],
            ['nombre' => 'Mostaza', 'icono' => '🌿', 'color' => 'yellow', 'descripcion' => 'Mostaza y derivados'],
            ['nombre' => 'Sésamo', 'icono' => '🌰', 'color' => 'orange', 'descripcion' => 'Semillas de sésamo'],
            ['nombre' => 'Sulfitos', 'icono' => '⚗️', 'color' => 'yellow', 'descripcion' => 'Conservantes a base de azufre'],
            ['nombre' => 'Altramuces', 'icono' => '🫘', 'color' => 'yellow', 'descripcion' => 'Altramuces y productos derivados'],
        ];
    }
}
