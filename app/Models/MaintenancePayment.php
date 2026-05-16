<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenancePayment extends Model
{
    public const STATUS_PAID = 'paid';
    public const STATUS_UNPAID = 'unpaid';

    protected $fillable = [
        'maintenance_period_id',
        'society_flat_id',
        'status',
        'paid_at',
        'transaction_id',
        'payment_note',
        'receipt_path',
        'receipt_original_name',
        'receipt_mime_type',
        'receipt_size',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'receipt_size' => 'integer',
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(MaintenancePeriod::class, 'maintenance_period_id');
    }

    public function flat(): BelongsTo
    {
        return $this->belongsTo(SocietyFlat::class, 'society_flat_id');
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function getHasReceiptAttribute(): bool
    {
        return ! empty($this->receipt_path);
    }
}
