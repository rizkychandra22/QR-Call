<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QrCode extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'shift_id',
        'radius_present_id',
        'qr_code_present',
        'present',
        'date',
        'start_time',
        'end_time',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }

    /**
     * Get the shift that owns this QR code.
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    /**
     * Get the allowed attendance radius/location for this QR code.
     */
    public function radiusPresent(): BelongsTo
    {
        return $this->belongsTo(RadiusPresent::class);
    }

    /**
     * Get all presents associated with this QR code.
     */
    public function presents(): HasMany
    {
        return $this->hasMany(Present::class);
    }
}
