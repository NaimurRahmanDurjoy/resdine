<template>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-lg font-bold text-gray-800">{{ pageTitle }}</h2>
        </div>

        <form @submit.prevent="$emit('submit', form)" class="p-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Left: Content Info -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Campaign Title *</label>
                        <input v-model="form.title" type="text" placeholder="e.g. Eid Mega Sale"
                            class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500" required>
                        <p v-if="form.errors.title" class="text-xs text-red-500 mt-1">{{ form.errors.title }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Target Link URL</label>
                        <input v-model="form.link_url" type="url" placeholder="https://example.com/promo"
                            class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                        <p v-if="form.errors.link_url" class="text-xs text-red-500 mt-1">{{ form.errors.link_url }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Campaign Type</label>
                            <select v-model="form.type"
                                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                                <option value="popup">Main Popup Modal</option>
                                <option value="banner">Inline Banner</option>
                                <option value="coupon">Coupon Promotion</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Priority (0-100)</label>
                            <input v-model="form.priority" type="number"
                                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="flex items-center gap-4 py-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" v-model="form.is_active" class="h-4 w-4 text-indigo-600 rounded">
                            <span class="text-sm font-medium text-gray-700">Campaign Active</span>
                        </label>
                    </div>
                </div>

                <!-- Right: Schedule & Image -->
                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Starts At</label>
                            <input v-model="form.starts_at" type="datetime-local"
                                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ends At</label>
                            <input v-model="form.ends_at" type="datetime-local"
                                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Marketing Image (Banner) *</label>
                        <ImageUploadPreview size="w-full aspect-[21/9]" shape="rounded-xl" name="image"
                            :existingImage="existingImage" @change="form.image = $event.target.files[0]" />
                        <p class="text-xs text-gray-500 mt-2 italic text-center">Recommended size: 1200x500px</p>
                        <p v-if="form.errors.image" class="text-xs text-red-500 mt-1">{{ form.errors.image }}</p>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 flex justify-end gap-3">
                <Link :href="route('admin.marketing-campaigns.index')"
                    class="px-6 py-2 border rounded-lg hover:bg-gray-50 transition">Cancel</Link>
                <button type="submit" :disabled="form.processing"
                    class="px-8 py-2 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition flex items-center gap-2 disabled:opacity-50">
                    <span class="material-symbols-outlined text-sm">save</span>
                    <span>Save Campaign</span>
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import ImageUploadPreview from '@/Components/ImageUploadPreview.vue'

const props = defineProps({
    form: Object,
    pageTitle: String,
    existingImage: { type: String, default: null }
})

defineEmits(['submit'])
</script>
