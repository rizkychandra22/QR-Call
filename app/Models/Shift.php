<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shift extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'shift_name',
        'shift_code',
        'in_time',
        'out_time',
    ];

    /**
     * Get all QR codes for this shift.
     */
    public function qrCodes(): HasMany
    {
        return $this->hasMany(QrCode::class);
    }

    /**
     * Get all presents for this shift.
     */
    public function presents(): HasMany
    {
        return $this->hasMany(Present::class);
    }
}
