<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocietyWingFloor extends Model
{
    protected $fillable = [
        'user_id',
        'society_wing_id',
        'floor_number',
        'flats_count',
    ];

    protected $casts = [
        'floor_number' => 'integer',
        'flats_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wing(): BelongsTo
    {
        return $this->belongsTo(SocietyWing::class, 'society_wing_id');
    }
}
