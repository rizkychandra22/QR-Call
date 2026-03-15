<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RadiusPresent extends Model
{
    protected $fillable = [
        'name',
        'lat',
        'lng',
        'radius',
    ];

    protected function casts(): array
    {
        return [
            'lat' => 'double',
            'lng' => 'double',
            'radius' => 'double',
        ];
    }

    public function qrCodes(): HasMany
    {
        return $this->hasMany(QrCode::class);
    }
}
