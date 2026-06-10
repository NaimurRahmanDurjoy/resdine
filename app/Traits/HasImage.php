<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait HasImage
{
    /**
     * Get the full URL for the image.
     */
    public function getImageUrlAttribute(): ?string
    {
        $field = $this->getImageField();
        if (!$this->$field) {
            return null;
        }

        // If the path stored in the database is already a full URL (e.g. Cloudinary)
        if (filter_var($this->$field, FILTER_VALIDATE_URL)) {
            return $this->$field;
        }

        // Try the configured disk; fall back to local storage for legacy images
        try {
            return Storage::disk(env('FILESYSTEM_DISK', 'public'))->url($this->$field);
        } catch (\Exception $e) {
            return asset('storage/' . $this->$field);
        }
    }

    /**
     * Determine which field stores the image path.
     * Override this in the model if the field is not 'image'.
     */
    protected function getImageField(): string
    {
        return property_exists($this, 'imageField') ? $this->imageField : 'image';
    }
}
