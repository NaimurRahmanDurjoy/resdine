<template>
  <form @submit.prevent="submit" class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6">
      <!-- Product Selection -->
      <div class="space-y-2">
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Menu Item</label>
        <select v-model="form.menu_item_id"
          class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-sm"
          :disabled="isEdit" @change="onProductChange">
          <option value="">Select Menu Item</option>
          <option v-for="item in menuItems" :key="item.id" :value="item.id">{{ item.name }}</option>
        </select>
        <p v-if="form.errors.menu_item_id" class="text-xs text-red-500 mt-1">{{ form.errors.menu_item_id }}</p>
      </div>

      <!-- Variant Selection -->
      <div class="space-y-2">
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Variant (Optional)</label>
        <select v-model="form.variant_id"
          class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-sm"
          :disabled="isEdit || !availableVariants.length">
          <option value="">Standard / All Variants</option>
          <option v-for="variant in availableVariants" :key="variant.id" :value="variant.id">{{ variant.name }}</option>
        </select>
        <p v-if="form.errors.variant_id" class="text-xs text-red-500 mt-1">{{ form.errors.variant_id }}</p>
      </div>
    </div>

    <!-- Recipe Items -->
    <div class="space-y-4">
      <div v-if="form.items.length > 0"
        class="border border-gray-100 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden">
        <div class="max-h-[400px] overflow-y-auto w-full custom-scrollbar">
          <table class="w-full text-left text-sm whitespace-nowrap relative">
            <thead
              class="bg-gray-50 dark:bg-gray-800/95 backdrop-blur text-gray-500 dark:text-gray-400 uppercase text-[10px] font-bold tracking-wider sticky top-0 z-10 shadow-sm border-b border-gray-200 dark:border-gray-700">
              <tr>
                <th class="px-4 py-3 min-w-[250px]">Inventory Item</th>
                <th class="px-4 py-3 w-32">Quantity</th>
                <th class="px-4 py-3 w-32">Unit</th>
                <th class="px-4 py-3 w-32">Wastage %</th>
                <th class="px-4 py-3 w-32">Row Cost</th>
                <th class="px-4 py-3 w-16 text-center">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50 bg-white dark:bg-gray-800/30">
              <tr v-for="(item, index) in form.items" :key="index"
                class="group hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors">

                <!-- Inventory Item Selection -->
                <td class="px-4 py-1 relative">
                  <select v-model="item.inventory_item_id"
                    class="block w-full px-3 py-1.5 border border-gray-200 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium"
                    @change="onItemChange($event, index)">
                    <option value="">Select Item</option>
                    <option v-for="i in inventoryItems" :key="i.id" :value="i.id">
                      {{ i.name }} ({{ i.item_type === 1 ? 'Ing' : (i.item_type === 3 ? 'Prep' : 'Other') }})
                    </option>
                  </select>
                </td>

                <!-- Quantity -->
                <td class="px-4 py-1">
                  <input v-model="item.quantity" type="number" step="0.001"
                    class="block w-full px-3 py-1.5 border border-gray-200 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium" />
                </td>

                <!-- Unit -->
                <td class="px-4 py-1">
                  <select v-model="item.unit_id"
                    class="block w-full px-3 py-1.5 border border-gray-200 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium"
                    :disabled="!item.inventory_item_id">
                    <option v-for="unit in getAllowedUnits(item)" :key="unit.id" :value="unit.id">
                      {{ unit.name }}
                    </option>
                  </select>
                </td>

                <!-- Wastage % -->
                <td class="px-4 py-1">
                  <div class="relative">
                    <input v-model="item.wastage_percentage" type="number" step="0.01" min="0" max="99.99"
                      class="block w-full px-3 py-1.5 pr-8 border border-gray-200 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium" />
                    <span
                      class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 text-xs font-bold">%</span>
                  </div>
                </td>

                <!-- Item Cost -->
                <td class="px-4 py-1">
                  <div
                    class="px-3 py-1.5 bg-gray-50 dark:bg-gray-900/40 rounded-lg text-sm font-bold text-gray-700 dark:text-gray-300 border border-transparent whitespace-nowrap">
                    {{ currency() }}{{ calculateItemCost(item).toFixed(2) }}
                  </div>
                </td>

                <!-- Remove Action -->
                <td class="px-4 py-1 text-center">
                  <button type="button" @click="removeItem(index)"
                    class="p-1.5 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors inline-flex justify-center items-center"
                    title="Remove">
                    <span class="material-symbols-outlined text-lg">delete</span>
                  </button>
                </td>

              </tr>
              <!-- Add Item Row -->
              <tr>
                <td colspan="6"
                  class="px-4 py-3 bg-gray-50/50 dark:bg-gray-800/30 border-t border-gray-100 dark:border-gray-700/50 text-center">
                  <button type="button" @click="addItem"
                    class="text-indigo-600 dark:text-indigo-400 text-sm font-semibold hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors inline-flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">add_circle</span> Add More Items
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else
        class="text-center py-10 bg-gray-50 dark:bg-gray-800/30 rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700">
        <span class="material-symbols-outlined text-4xl text-gray-400 mb-2">kitchen</span>
        <p class="text-gray-500 dark:text-gray-400 text-sm">No ingredients added yet.</p>
        <button type="button" @click="addItem"
          class="mt-3 px-4 py-2 bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400 rounded-lg text-sm font-semibold hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors inline-flex items-center gap-1">
          <span class="material-symbols-outlined text-sm">add</span> Add First Ingredient
        </button>
      </div>

      <p v-if="form.errors.items"
        class="text-sm text-red-500 bg-red-50 dark:bg-red-900/20 p-3 rounded-lg border border-red-100 dark:border-red-900/30">
        {{ form.errors.items }}</p>

      <!-- Cost Summary Section -->
      <div v-if="form.items.length > 0" class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div
          class="p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl border border-indigo-100 dark:border-indigo-900/30">
          <p class="text-[10px] font-bold uppercase tracking-wider text-indigo-400 mb-1">Total Food Cost</p>
          <div class="text-2xl font-black text-indigo-700 dark:text-indigo-300">{{ currency() }}{{ totalCost.toFixed(2)
          }}</div>
        </div>
        <div
          class="p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl border border-emerald-100 dark:border-emerald-900/30">
          <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-500 mb-1">Selling Price</p>
          <div class="text-2xl font-black text-emerald-700 dark:text-emerald-300">{{ currency() }}{{
            sellingPrice.toFixed(2) }}</div>
        </div>
        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-900/30">
          <p class="text-[10px] font-bold uppercase tracking-wider text-blue-500 mb-1">Gross Profit (GP%)</p>
          <div :class="[
            'text-2xl font-black',
            marginPercentage >= 75 ? 'text-green-600' : (marginPercentage >= 65 ? 'text-amber-500' : 'text-red-500')
          ]">
            {{ marginPercentage.toFixed(2) }}%
          </div>
        </div>
      </div>
    </div>

    <!-- Submit Section -->
    <div
      class="sticky bottom-0 z-30 flex justify-end p-4 mt-6 bg-white/95 dark:bg-gray-900/95 backdrop-blur border-t border-gray-200 dark:border-gray-700 space-x-3 -mx-4 px-4 sm:mx-0 sm:px-6 sm:rounded-b-xl shadow-[0_-10px_15px_-3px_rgba(0,0,0,0.05)] dark:shadow-[0_-5px_15px_rgba(0,0,0,0.3)]">
      <Link :href="route('admin.recipes.index')"
        class="px-6 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition">
        Cancel
      </Link>
      <button :disabled="form.processing"
        class="px-8 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 disabled:opacity-50 transition shadow-md flex items-center gap-2">
        <span v-if="form.processing" class="animate-spin material-symbols-outlined text-sm">sync</span>
        <span>{{ isEdit ? 'Update Recipe' : 'Save Recipe' }}</span>
      </button>
    </div>
  </form>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'

const props = defineProps({
  form: Object,
  menuItems: Array,
  inventoryItems: Array,
  units: Array,
  branches: Array,
  isEdit: {
    type: Boolean,
    default: false
  },
  initialProductId: [String, Number]
})

const emit = defineEmits(['submit'])

// UI State
const totalCost = computed(() => {
  const total = props.form.items.reduce((acc, item) => acc + calculateItemCost(item), 0)
  return Number(total)
})

const selectedProduct = computed(() => {
  return props.menuItems.find(item => item.id == props.form.menu_item_id)
})

const selectedVariant = computed(() => {
  return selectedProduct.value?.variants?.find(v => v.id == props.form.variant_id)
})

const sellingPrice = computed(() => {
  let price = 0
  if (props.form.variant_id) {
    price = selectedVariant.value?.price || selectedProduct.value?.price || 0
  } else {
    price = selectedProduct.value?.price || 0
  }
  return Number(price)
})

const marginPercentage = computed(() => {
  if (sellingPrice.value <= 0) return 0
  return ((sellingPrice.value - totalCost.value) / sellingPrice.value) * 100
})

const availableVariants = computed(() => {
  return selectedProduct.value?.variants || []
})

const currentIngredients = computed(() => {
  return props.ingredients
})

// Methods
const getAllowedUnits = (item) => {
  const source = props.inventoryItems.find(i => i.id == item.inventory_item_id)
  if (!source) return []

  const baseUnitId = source.unit?.base_unit_id || source.unit_id
  return props.units.filter(u => u.id == baseUnitId || u.base_unit_id == baseUnitId)
}

const calculateItemCost = (item) => {
  const source = props.inventoryItems.find(i => i.id == item.inventory_item_id)
  if (!source || !item.quantity) return 0

  // baseCost is per source's default unit
  const baseCost = source.latest_cost || 0
  const sourceUnit = props.units.find(u => u.id == source.unit_id)
  const selectedUnit = props.units.find(u => u.id == item.unit_id)

  let unitCost = baseCost

  if (selectedUnit && sourceUnit && selectedUnit.id != sourceUnit.id) {
    const costPerBase = baseCost / (sourceUnit.conversion_factor || 1)
    unitCost = costPerBase * (selectedUnit.conversion_factor || 1)
  }

  const wastage = parseFloat(item.wastage_percentage) || 0
  const grossQty = wastage < 100 ? (item.quantity / (1 - (wastage / 100))) : item.quantity

  return Number(unitCost * grossQty)
}

const onProductChange = () => {
  props.form.variant_id = ''
}

const onItemChange = (event, index) => {
  const itemId = event.target.value
  const item = props.inventoryItems.find(i => i.id == itemId)
  if (item) {
    props.form.items[index].unit_id = item.unit_id
  }
}

const addItem = () => {
  props.form.items.push({
    inventory_item_id: '',
    quantity: 1,
    unit_id: '',
    wastage_percentage: 0
  })
}

const removeItem = (index) => {
  props.form.items.splice(index, 1)
}

const submit = () => {
  emit('submit')
}

onMounted(() => {
  if (props.initialProductId && !props.isEdit) {
    props.form.menu_item_id = props.initialProductId
  }
})
</script>
