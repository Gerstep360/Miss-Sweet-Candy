<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IpWhitelist extends Model
{
    use HasFactory;

    protected $table = 'ip_whitelist';
    public $timestamps = false;
    
    protected $fillable = [
        'user_id',
        'ip_cidr'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Verificar si una IP está en la whitelist para un usuario
     */
    public static function isIpAllowed($userId, $ip): bool
    {
        return static::where('user_id', $userId)
            ->where('ip_cidr', $ip)
            ->exists();
    }

    /**
     * Obtener todas las IPs permitidas para un usuario
     */
    public static function getAllowedIps($userId): array
    {
        return static::where('user_id', $userId)
            ->pluck('ip_cidr')
            ->toArray();
    }
}