<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Producto extends Model
{
    protected $fillable = [
        'categoria_id', 'nombre', 'unidad', 'precio', 'imagen'
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

        // Método optimizado para obtener la URL de la imagen
    public function getImagenUrlAttribute()
    {
        $ruta = $this->imagen ? 'storage/' . $this->imagen : 'storage/img/none/none.png';

        // Verifica si el archivo existe en public/storage
        if ($this->imagen && !File::exists(public_path($ruta))) {
            return asset('storage/img/none/none.png');
        }

        return asset($ruta);
    }
    public function pedidoItems()
{
    return $this->hasMany(\App\Models\PedidoItem::class);
}
}
