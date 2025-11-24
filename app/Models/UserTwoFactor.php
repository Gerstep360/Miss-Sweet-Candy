<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTwoFactor extends Model
{
    use HasFactory;

    protected $table = 'user_two_factor';
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    
    protected $fillable = [
        'user_id',
        'secret',
        'recovery_codes'
    ];

    protected $casts = [
        'recovery_codes' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Verificar si tiene códigos de respaldo
     */
    public function hasRecoveryCodes(): bool
    {
        return !empty($this->recovery_codes);
    }

    /**
     * Obtener códigos de respaldo formateados
     */
    public function getFormattedRecoveryCodes(): array
    {
        return array_map(function ($code) {
            return [
                'code' => $code,
                'used' => false // Puedes extender esto para marcar códigos usados
            ];
        }, $this->recovery_codes ?? []);
    }
}