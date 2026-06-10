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
            if (Storage::disk($disk)->exists($path)) {
                return Storage::disk($disk)->delete($path);
            }
        } catch (\Exception $e) {
            // Ignore — file may be on local storage from before the migration
        }

        // Fall back to local public disk for legacy images
        if ($disk !== 'public' && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }
}
