<template>
  <div class="flex flex-col md:flex-row md:items-center gap-4">
    <div :class="[size, shape, 'overflow-hidden']">
      <!-- Image Preview -->
      <template v-if="imageUrl">
        <img :src="imageUrl" alt="Preview Image" class="w-full h-full object-cover" />
      </template>

      <template v-else-if="existingImage">
        <img :src="existingImageUrl" alt="Current Image" class="w-full h-full object-cover" />
      </template>

      <template v-else>
        <div class="w-full h-full p-2 bg-gray-100 rounded flex items-center justify-center text-gray-400">
          No Image Selected
        </div>
      </template>
    </div>

    <!-- File Input -->
    <input type="file" :name="name" accept="image/*" @change="previewImage" class="block mt-2 text-sm text-gray-600" />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  name: { type: String, default: 'image' },
  existingImage: { type: String, default: null },
  size: { type: String, default: 'w-32 h-32' },
  shape: { type: String, default: 'rounded' }
})

const imageUrl = ref(null)

const existingImageUrl = computed(() => {
  if (!props.existingImage) return null
  // If it's already a full URL or data URI, return as is
  if (props.existingImage.startsWith('http') || props.existingImage.startsWith('data:') || props.existingImage.startsWith('/')) {
    return props.existingImage
  }
  return `/storage/${props.existingImage}`
})

function previewImage(event) {
  const file = event.target.files[0]
  if (!file) {
    imageUrl.value = null
    return
  }
  imageUrl.value = URL.createObjectURL(file)
}
</script>
