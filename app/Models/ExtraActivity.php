<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExtraActivity extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'activity_type',
        'amount_per_flat',
        'target_amount',
        'due_date',
        'notes',
    ];

    protected $casts = [
        'amount_per_flat' => 'decimal:2',
        'target_amount' => 'decimal:2',
        'due_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ExtraActivityPayment::class);
    }

    public static function types(): array
    {
        return [
            'building_repair' => 'Building repair fund',
            'function_event' => 'Function / event collection',
            'painting' => 'Painting work',
            'lift_work' => 'Lift repair / service',
            'water_tank' => 'Water tank work',
            'security_upgrade' => 'Security upgrade',
            'other' => 'Other activity',
        ];
    }

    public function getTypeLabelAttribute(): string
    {
        return self::types()[$this->activity_type] ?? 'Other activity';
    }
}
