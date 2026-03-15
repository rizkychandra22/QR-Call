<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Present extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'shift_id',
        'qr_code_id',
        'date',
        'time',
        'hours',
        'present_desc_system',
        'present_user_desc',
        'present_user_image',
        'status',
        'status_desc',
        'status_image',
        'lat_location_present',
        'lng_location_present',
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
            'lat_location_present' => 'double',
            'lng_location_present' => 'double',
        ];
    }

    /**
     * Get the user that owns this present record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the shift that owns this present record.
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    /**
     * Get the QR code associated with this present record.
     */
    public function qrCode(): BelongsTo
    {
        return $this->belongsTo(QrCode::class);
    }
}
