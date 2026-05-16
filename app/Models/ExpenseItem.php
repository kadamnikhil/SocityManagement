<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseItem extends Model
{
    public const CATEGORY_STAFF_SALARY = 'staff_salary';
    public const CATEGORY_PROGRAMME = 'programme';
    public const CATEGORY_LIGHT_BILL = 'light_bill';
    public const CATEGORY_REPAIR = 'repair';
    public const CATEGORY_CLEANING = 'cleaning';
    public const CATEGORY_SECURITY = 'security';
    public const CATEGORY_WATER = 'water';
    public const CATEGORY_OTHER = 'other';

    protected $fillable = [
        'expense_period_id',
        'category',
        'title',
        'payee_name',
        'amount',
        'expense_date',
        'payment_mode',
        'reference_no',
        'note',
        'bill_path',
        'bill_original_name',
        'bill_mime_type',
        'bill_size',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'bill_size' => 'integer',
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(ExpensePeriod::class, 'expense_period_id');
    }

    public static function categories(): array
    {
        return [
            self::CATEGORY_STAFF_SALARY => 'Salary of staff',
            self::CATEGORY_PROGRAMME => 'Programme / event',
            self::CATEGORY_LIGHT_BILL => 'Building light bill',
            self::CATEGORY_REPAIR => 'Repair & maintenance',
            self::CATEGORY_CLEANING => 'Cleaning / housekeeping',
            self::CATEGORY_SECURITY => 'Security expense',
            self::CATEGORY_WATER => 'Water / tanker bill',
            self::CATEGORY_OTHER => 'Other expense',
        ];
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::categories()[$this->category] ?? 'Other expense';
    }
}
