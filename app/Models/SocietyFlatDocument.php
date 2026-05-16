<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class SocietyFlatDocument extends Model
{
    protected $fillable = [
        'user_id',
        'society_flat_id',
        'name',
        'file_path',
        'file_original_name',
        'file_size',
        'mime_type',
        'sort_order',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'sort_order' => 'integer',
    ];

    public function flat(): BelongsTo
    {
        return $this->belongsTo(SocietyFlat::class, 'society_flat_id');
    }

    public function getFileUrlAttribute(): ?string
    {
        if (! $this->file_path) {
            return null;
        }
        return Storage::disk('public')->url($this->file_path);
    }

    public function getFileSizeHumanAttribute(): string
    {
        $size = (int) $this->file_size;
        if ($size <= 0) {
            return '—';
        }
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }
        return number_format($size, $i === 0 ? 0 : 1).' '.$units[$i];
    }

    public function getIconClassAttribute(): string
    {
        $mime = (string) ($this->mime_type ?? '');
        $orig = strtolower((string) $this->file_original_name);
        if (str_contains($mime, 'pdf') || str_ends_with($orig, '.pdf')) {
            return 'ti-file-type-pdf text-danger';
        }
        if (str_starts_with($mime, 'image/')) {
            return 'ti-photo text-success';
        }
        if (str_contains($mime, 'word') || preg_match('/\.docx?$/i', $orig)) {
            return 'ti-file-type-doc text-primary';
        }
        if (str_contains($mime, 'sheet') || str_contains($mime, 'excel') || preg_match('/\.xlsx?$/i', $orig)) {
            return 'ti-file-type-xls text-success';
        }
        return 'ti-file text-secondary';
    }
}
