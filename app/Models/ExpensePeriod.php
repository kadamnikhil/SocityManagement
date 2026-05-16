<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpensePeriod extends Model
{
    protected $fillable = [
        'user_id',
        'month',
        'year',
        'notes',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ExpenseItem::class);
    }

    public function getMonthNameAttribute(): string
    {
        return date('F', mktime(0, 0, 0, (int) $this->month, 1));
    }

    public function getLabelAttribute(): string
    {
        return $this->month_name.' '.$this->year;
    }
}
