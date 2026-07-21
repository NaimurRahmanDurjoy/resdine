<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatabaseBackup extends Model
{
    protected $fillable = [
        'filename',
        'db_host',
        'db_port',
        'db_name',
        'db_username',
        'file_path',
        'file_size',
        'backup_type',
        'status',
        'error_message',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get human-readable backup type label.
     */
    public function getBackupTypeLabelAttribute(): string
    {
        return match ($this->backup_type) {
            'data_only'       => 'Data Only',
            'structure_only'  => 'Structure Only',
            'complete'        => 'Complete',
            default           => 'Unknown',
        };
    }

    /**
     * Get human-readable file size.
     */
    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
