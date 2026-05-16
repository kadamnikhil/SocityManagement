<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SocietyWing extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'label',
        'floors_count',
        'sort_order',
    ];

    protected $casts = [
        'floors_count' => 'integer',
        'sort_order' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function floors(): HasMany
    {
        return $this->hasMany(SocietyWingFloor::class)->orderBy('floor_number');
    }

    public function flats(): HasMany
    {
        return $this->hasMany(SocietyFlat::class);
    }
}
