<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExtraActivityPayment extends Model
{
    public const STATUS_PAID = 'paid';
    public const STATUS_UNPAID = 'unpaid';

    protected $fillable = [
        'extra_activity_id',
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

    public function activity(): BelongsTo
    {
        return $this->belongsTo(ExtraActivity::class, 'extra_activity_id');
    }

    public function flat(): BelongsTo
    {
        return $this->belongsTo(SocietyFlat::class, 'society_flat_id');
    }
}
