<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SocietyFlat extends Model
{
    protected $fillable = [
        'user_id',
        'society_wing_id',
        'floor_number',
        'flat_index',
        'unit_code',
        'owner_name',
        'owner_mobile',
        'owner_email',
        'vehicles_count',
        'vehicles_2w',
        'vehicles_3w',
        'vehicles_4w',
        'sort_order',
    ];

    protected $casts = [
        'floor_number' => 'integer',
        'flat_index' => 'integer',
        'sort_order' => 'integer',
        'vehicles_count' => 'integer',
        'vehicles_2w' => 'integer',
        'vehicles_3w' => 'integer',
        'vehicles_4w' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wing(): BelongsTo
    {
        return $this->belongsTo(SocietyWing::class, 'society_wing_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(SocietyFlatDocument::class, 'society_flat_id')->orderBy('sort_order')->orderBy('id');
    }

    public function maintenancePayments(): HasMany
    {
        return $this->hasMany(MaintenancePayment::class, 'society_flat_id');
    }
}
