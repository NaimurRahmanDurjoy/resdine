<template>
  <div class="space-y-2">
    <!-- Image Preview -->
    <template v-if="imageUrl">
      <img :src="imageUrl" alt="Preview Image" class="w-50 h-50 object-cover rounded" />
    </template>

    <template v-else-if="existingImage">
      <img :src="existingImageUrl" alt="Current Image" class="w-50 h-50 object-cover rounded" />
    </template>

    <template v-else>
      <div class="w-50 h-50 bg-gray-100 rounded flex items-center justify-center text-gray-400">
        No Image Selected
      </div>
    </template>

    <!-- File Input -->
    <input
      type="file"
      :name="name"
      accept="image/*"
      @change="previewImage"
      class="block mt-2 text-sm text-gray-600"
    />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  name: { type: String, default: 'image' },
  existingImage: { type: String, default: null }
})

const imageUrl = ref(null)

const existingImageUrl = computed(() => {
  return props.existingImage ? `/storage/${props.existingImage}` : null
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
