<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function luponCase(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(LuponCase::class);
    }

    public function hearing(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Hearing::class);
    }

    public function uploader(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Return the download URL for the uploaded file, or '#' if path is absent.
     */
    public function getViewerUrlAttribute(): string
    {
        if (! $this->file_path || $this->file_path === 'digital_record') {
            return '#';
        }

        return route('media.show', ['path' => $this->file_path]);
    }

    /**
     * Return the download route for this document.
     */
    public function getDownloadUrlAttribute(): string
    {
        return route('cases.documents.download', [
            'case' => $this->lupon_case_id,
            'document' => $this->id,
        ]);
    }

    /**
     * Return the open-in-external-app JSON route for Electron.
     */
    public function getOpenUrlAttribute(): string
    {
        return route('cases.documents.open', [
            'case' => $this->lupon_case_id,
            'document' => $this->id,
        ]);
    }

    /**
     * Return a human-readable file size string (e.g. "2.4 MB").
     */
    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size ?? 0;

        if ($bytes === 0) {
            return '—';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 1).' '.$units[$i];
    }

    /**
     * Return a Phosphor icon class based on mime type or file extension.
     */
    public function getFileIconAttribute(): string
    {
        $mime = $this->mime_type ?? '';
        $ext = strtolower(pathinfo($this->filename ?? '', PATHINFO_EXTENSION));

        if (str_contains($mime, 'pdf') || $ext === 'pdf') {
            return 'ph-file-pdf';
        }

        if (in_array($ext, ['doc', 'docx']) || str_contains($mime, 'word') || str_contains($mime, 'officedocument.wordprocessing')) {
            return 'ph-file-doc';
        }

        if (in_array($ext, ['xls', 'xlsx']) || str_contains($mime, 'spreadsheet') || str_contains($mime, 'excel')) {
            return 'ph-file-xls';
        }

        if ($ext === 'odt' || str_contains($mime, 'opendocument')) {
            return 'ph-file-text';
        }

        if (str_contains($mime, 'image/') || in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            return 'ph-file-image';
        }

        return 'ph-file';
    }

    /**
     * Return the CSS colour associated with this file type for icon theming.
     */
    public function getFileIconColorAttribute(): string
    {
        return match ($this->file_icon) {
            'ph-file-pdf' => '#ef4444',
            'ph-file-doc' => '#2563eb',
            'ph-file-xls' => '#16a34a',
            'ph-file-image' => '#7c3aed',
            default => '#6b7280',
        };
    }
}
