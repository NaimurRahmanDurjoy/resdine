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
        return $this->$field ? asset('storage/' . $this->$field) : null;
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
