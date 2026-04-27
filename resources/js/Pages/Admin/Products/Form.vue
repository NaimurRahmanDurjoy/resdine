<template>
  <form @submit.prevent="$emit('submit', form)" class="space-y-8 pb-12">
    <!-- Main Form Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

      <!-- Left Column: Basic Info -->
      <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 space-y-6 h-fit">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 border-b pb-2">Basic Information</h3>

        <!-- Category -->
        <div class="flex items-start gap-6">
          <label class="w-32 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Category *</label>
          <div class="flex-1 space-y-1">
            <select v-model="form.category_id"
              class="w-full h-10 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 text-sm"
              :class="{ 'border-red-500 focus:ring-red-500': form.errors.category_id }" required>
              <option value="">Select Category</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>
            <p v-if="form.errors.category_id" class="text-xs text-red-500">{{ form.errors.category_id }}</p>
          </div>
        </div>

        <!-- Type -->
        <div class="flex items-start gap-6">
          <label class="w-32 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Type *</label>
          <div class="flex-1">
            <select v-model="form.type"
              class="w-full h-10 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 text-sm"
              required>
              <option :value="1">Regular Menu Item</option>
              <option :value="2">Combo Meal</option>
              <option :value="3">Complementary Item</option>
            </select>
          </div>
        </div>

        <!-- Name -->
        <div class="flex items-start gap-6">
          <label class="w-32 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Name *</label>
          <div class="flex-1 space-y-1">
            <input v-model="form.name" type="text" placeholder="Enter menu item name"
              class="w-full h-10 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 text-sm"
              :class="{ 'border-red-500 focus:ring-red-500': form.errors.name }" required />
            <p v-if="form.errors.name" class="text-xs text-red-500">{{ form.errors.name }}</p>
          </div>
        </div>

        <!-- Description -->
        <div class="flex items-start gap-6">
          <label class="w-32 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
          <div class="flex-1">
            <textarea v-model="form.description" rows="3" placeholder="Describe your menu item..."
              class="w-full border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 py-2 text-sm"></textarea>
          </div>
        </div>

        <!-- Status & Featured -->
        <div class="flex items-start gap-6 pt-2">
          <label class="w-32 text-sm font-medium text-gray-700 dark:text-gray-300">Options</label>
          <div class="flex-1 flex gap-8">
            <div class="flex items-center">
              <input v-model="form.status" type="checkbox" id="status" :true-value="1" :false-value="0"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded cursor-pointer">
              <label for="status" class="ml-2 block text-sm text-gray-700 dark:text-gray-400 cursor-pointer">
                Active
              </label>
            </div>
            <div class="flex items-center">
              <input v-model="form.is_featured" type="checkbox" id="is_featured" :true-value="1" :false-value="0"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded cursor-pointer">
              <label for="is_featured" class="ml-2 block text-sm text-gray-700 dark:text-gray-400 cursor-pointer">
                Featured
              </label>
            </div>
            <div class="flex items-center">
              <input v-model="form.is_prep_item" type="checkbox" id="is_prep_item" :true-value="1" :false-value="0"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded cursor-pointer">
              <label for="is_prep_item" class="ml-2 block text-sm text-gray-700 dark:text-gray-400 cursor-pointer">
                Is Prep Item
              </label>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Price & Details -->
      <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 space-y-6 h-fit">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 border-b pb-2">Price & Details</h3>

        <!-- Price -->
        <div class="flex items-start gap-6">
          <label class="w-32 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Price *</label>
          <div class="flex-1 space-y-1">
            <div class="relative">
              <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">$</span>
              <input v-model="form.price" type="number" step="0.01" placeholder="0.00"
                class="w-full h-10 pl-8 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 text-sm"
                :class="{ 'border-red-500 focus:ring-red-500': form.errors.price }" required />
            </div>
            <p v-if="form.errors.price" class="text-xs text-red-500">{{ form.errors.price }}</p>
          </div>
        </div>

        <!-- Cost Price -->
        <div class="flex items-start gap-6">
          <label class="w-32 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Cost Price</label>
          <div class="flex-1 relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">$</span>
            <input v-model="form.cost_price" type="number" step="0.01" placeholder="0.00"
              class="w-full h-10 pl-8 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 text-sm" />
          </div>
        </div>

        <!-- Unit -->
        <div class="flex items-start gap-6">
          <label class="w-32 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Unit *</label>
          <div class="flex-1">
            <select v-model="form.unit_id"
              class="w-full h-10 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 text-sm"
              required>
              <option value="">Select Unit</option>
              <option v-for="unit in units" :key="unit.id" :value="unit.id">{{ unit.name }}</option>
            </select>
          </div>
        </div>

        <!-- Department -->
        <div class="flex items-start gap-6">
          <label class="w-32 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Department *</label>
          <div class="flex-1">
            <select v-model="form.department_id"
              class="w-full h-10 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 text-sm"
              required>
              <option value="">Select Department</option>
              <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
            </select>
          </div>
        </div>

        <!-- Image -->
        <div class="flex items-start gap-6 pt-2">
          <label class="w-32 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Product Image</label>
          <div class="flex-1 space-y-1">
            <ImageUploadPreview size="w-32 h-32" shape="rounded" name="menu_img" :existingImage="existingImage"
              @change="form.menu_img = $event.target.files[0]" />
            <p v-if="form.errors.menu_img" class="text-xs text-red-500">{{ form.errors.menu_img }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Combo Items Section (Conditional) -->
    <div v-if="form.type == 2"
      class="bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-dashed border-gray-300 dark:border-gray-600 p-8 space-y-6">
      <div class="flex items-center gap-2 mb-2">
        <span class="material-symbols-outlined text-indigo-600">layers</span>
        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">Combo Meal Settings</h3>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Multi-select Menu Items -->
        <div class="space-y-2">
          <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Select Combo Items *</label>
          <div class="border rounded-lg bg-white dark:bg-gray-900 overflow-hidden shadow-inner">
            <div class="max-h-60 overflow-y-auto p-2 space-y-1">
              <label v-for="item in menuItems" :key="item.id"
                class="flex items-center p-2 rounded hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer transition-colors"
                :class="{ 'bg-indigo-50 dark:bg-indigo-900/20': form.combo_items.includes(item.id) }">
                <input type="checkbox" :value="item.id" v-model="form.combo_items"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                  {{ item.name }}
                  <span class="text-gray-400 dark:text-gray-500 ml-1">(${{ parseFloat(item.price).toFixed(2) }})</span>
                </span>
              </label>
            </div>
          </div>
          <p class="text-xs text-gray-500 dark:text-gray-500 italic">Select items included in this combo deal</p>
        </div>

        <!-- Combo Pricing -->
        <div class="space-y-6">
          <div class="space-y-1">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Combo Discount (%)</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">%</span>
              <input v-model="form.combo_discount" type="number" min="0" max="100" placeholder="0"
                class="w-full h-10 pl-8 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm" />
            </div>
          </div>

          <div class="space-y-1">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Final Combo Price</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">$</span>
              <input v-model="form.combo_final_price" type="number" step="0.01" placeholder="0.00"
                class="w-full h-10 pl-8 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-bold text-indigo-600" />
            </div>
            <p class="text-xs text-gray-500 italic">Overrides the standard price if set</p>
          </div>

          <!-- Summary Info -->
          <div
            class="p-4 bg-indigo-50 dark:bg-indigo-900/10 rounded-lg border border-indigo-100 dark:border-indigo-900/30">
            <div class="flex justify-between items-center text-sm font-medium text-indigo-800 dark:text-indigo-300">
              <span>Selected Items:</span>
              <span>{{ form.combo_items.length }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Form Actions -->
    <div
      class="flex justify-start gap-4 sticky bottom-4 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-lg">
      <Link :href="route('admin.product.items.index')"
        class="px-6 py-2 rounded-lg text-sm border bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600 transition font-medium">
        Cancel
      </Link>
      <button type="submit" :disabled="form.processing"
        class="px-8 py-2 rounded-lg text-sm bg-indigo-600 text-white font-bold shadow-indigo-200 hover:bg-indigo-700 transition disabled:opacity-50 flex items-center">
        <span class="material-symbols-outlined mr-2">save</span>
        {{ isEdit ? 'Update Menu Item' : 'Create Menu Item' }}
      </button>
    </div>
  </form>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import ImageUploadPreview from '@/Components/ImageUploadPreview.vue'

const props = defineProps({
  form: Object,
  categories: Array,
  units: Array,
  departments: Array,
  menuItems: Array,
  existingImage: { type: String, default: null },
  isEdit: { type: Boolean, default: false }
})

defineEmits(['submit'])
</script>
