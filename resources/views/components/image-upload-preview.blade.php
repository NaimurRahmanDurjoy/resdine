@props(['name' => 'image', 'existingImage' => null])

<div x-data="imagePreview()" class="space-y-2">
    <!-- Image Preview -->
    <template x-if="imageUrl">
        <img :src="imageUrl" alt="Preview Image" class="w-50 h-50 object-cover rounded" />
    </template>

    <template x-if="!imageUrl && '{{ $existingImage }}'">
        <img src="{{ asset($existingImage) }}" alt="Current Image" class="w-50 h-50 object-cover rounded" />
    </template>

    <template x-if="!imageUrl && ! '{{ $existingImage }}'">
        <div class="w-50 h-50 bg-gray-100 rounded flex items-center justify-center text-gray-400">
            No Image Selected
        </div>
    </template>

    <!-- File Input -->
    <input
        type="file"
        name="{{ $name }}"
        accept="image/*"
        @change="previewImage"
        class="block mt-2 text-sm text-gray-600"
    />
</div>

<script>
    function imagePreview() {
        return {
            imageUrl: null,
            previewImage(event) {
                const file = event.target.files[0];
                if (!file) {
                    this.imageUrl = null;
                    return;
                }
                this.imageUrl = URL.createObjectURL(file);
            }
        }
    }
</script>
