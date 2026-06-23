<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadService
{
    /**
     * Upload an image and return the relative path.
     */
    public function upload(UploadedFile $file, string $folder = 'uploads'): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs($folder, $filename, env('FILESYSTEM_DISK', 'public'));
    }

    /**
     * Delete an image if it exists.
     */
    public function delete(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        $disk = env('FILESYSTEM_DISK', 'public');

        // Try configured disk first
        try {
            // Delete directly - most cloud drivers handle missing files gracefully or return false
            // This saves one API call (exists()) per deletion
            return Storage::disk($disk)->delete($path);
        } catch (\Exception $e) {
            // If it's a structural error, we might want to try public disk
        }

        // Fall back to local public disk for legacy images only if disk is not public
        if ($disk !== 'public') {
            try {
                if (Storage::disk('public')->exists($path)) {
                    return Storage::disk('public')->delete($path);
                }
            } catch (\Exception $e) {}
        }

        return false;
    }
}
