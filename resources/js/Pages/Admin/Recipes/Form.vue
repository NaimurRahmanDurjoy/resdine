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
      <div
        class="flex justify-between items-center bg-gray-50 dark:bg-gray-800/50 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
        <h3 class="text-md font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
          <span class="material-symbols-outlined text-indigo-500">inventory_2</span>
          Ingredients & Quantities
        </h3>
        <button type="button" @click="addItem"
          class="px-3 py-1.5 bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400 rounded-lg text-xs font-bold hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors flex items-center gap-1 border border-indigo-100 dark:border-indigo-800">
          <span class="material-symbols-outlined text-sm">add</span> Add Ingredient
        </button>
      </div>

      <div class="space-y-3">
        <div v-for="(item, index) in form.items" :key="index"
          class="flex items-start gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm relative group">

          <div class="flex-grow grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Ingredient -->
            <div class="space-y-1">
              <label
                class="text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Ingredient</label>
              <select v-model="item.ingredient_id"
                class="block w-full px-3 py-1.5 border border-gray-200 dark:border-gray-600 rounded-lg text-sm bg-gray-50 dark:bg-gray-700 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium"
                @change="onIngredientChange($event, index)">
                <option value="">Select Ingredient</option>
                <option v-for="ing in ingredients" :key="ing.id" :value="ing.id">{{ ing.name }}</option>
              </select>
            </div>

            <!-- Quantity -->
            <div class="space-y-1">
              <label
                class="text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Quantity</label>
              <input v-model="item.quantity" type="number" step="0.001"
                class="block w-full px-3 py-1.5 border border-gray-200 dark:border-gray-600 rounded-lg text-sm bg-gray-50 dark:bg-gray-700 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium" />
            </div>

            <!-- Unit -->
            <div class="space-y-1">
              <label
                class="text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Unit</label>
              <select v-model="item.unit_id"
                class="block w-full px-3 py-1.5 border border-gray-200 dark:border-gray-600 rounded-lg text-sm bg-gray-50 dark:bg-gray-700 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium"
                :disabled="!item.ingredient_id">
                <option v-for="unit in getAllowedUnits(item.ingredient_id)" :key="unit.id" :value="unit.id">
                  {{ unit.name }}
                </option>
              </select>
            </div>

            <!-- Wastage % -->
            <div class="space-y-1">
              <label class="text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Wastage
                %</label>
              <div class="relative">
                <input v-model="item.wastage_percentage" type="number" step="0.01" min="0" max="99.99"
                  class="block w-full px-3 py-1.5 pr-8 border border-gray-200 dark:border-gray-600 rounded-lg text-sm bg-gray-50 dark:bg-gray-700 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium" />
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-bold">%</span>
              </div>
            </div>

            <!-- Item Cost -->
            <div class="space-y-1">
              <label class="text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Row Cost</label>
              <div class="px-3 py-1.5 bg-gray-50 dark:bg-gray-900/50 rounded-lg text-sm font-bold text-gray-700 dark:text-gray-300 border border-transparent">
                ${{ calculateItemCost(item).toFixed(2) }}
              </div>
            </div>
          </div>

          <button type="button" @click="removeItem(index)"
            class="mt-6 p-1.5 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
            title="Remove">
            <span class="material-symbols-outlined text-lg">delete</span>
          </button>
        </div>
      </div>

      <p v-if="form.errors.items"
        class="text-sm text-red-500 bg-red-50 dark:bg-red-900/20 p-3 rounded-lg border border-red-100 dark:border-red-900/30">
        {{ form.errors.items }}</p>

      <!-- Cost Summary Section -->
      <div v-if="form.items.length > 0" class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl border border-indigo-100 dark:border-indigo-900/30">
          <p class="text-[10px] font-bold uppercase tracking-wider text-indigo-400 mb-1">Total Food Cost</p>
          <div class="text-2xl font-black text-indigo-700 dark:text-indigo-300">${{ totalCost.toFixed(2) }}</div>
        </div>
        <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl border border-emerald-100 dark:border-emerald-900/30">
          <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-500 mb-1">Selling Price</p>
          <div class="text-2xl font-black text-emerald-700 dark:text-emerald-300">${{ sellingPrice.toFixed(2) }}</div>
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
    <div class="flex justify-end pt-6 border-t border-gray-100 dark:border-gray-700 space-x-3">
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
  ingredients: Array,
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
const getAllowedUnits = (ingredientId) => {
  const ingredient = props.ingredients.find(ing => ing.id == ingredientId)
  if (!ingredient) return []
  
  const baseUnitId = ingredient.unit?.base_unit_id || ingredient.unit_id
  return props.units.filter(u => u.id == baseUnitId || u.base_unit_id == baseUnitId)
}

const calculateItemCost = (item) => {
  const ingredient = props.ingredients.find(ing => ing.id == item.ingredient_id)
  if (!ingredient || !item.quantity) return 0
  
  // baseCost is per ingredient's default unit
  const baseCost = ingredient.latest_cost || 0
  const ingredientUnit = props.units.find(u => u.id == ingredient.unit_id)
  const selectedUnit = props.units.find(u => u.id == item.unit_id)
  
  let unitCost = baseCost
  
  if (selectedUnit && ingredientUnit && selectedUnit.id != ingredientUnit.id) {
    // 1. Convert cost per ingredient unit to cost per absolute base unit
    // If cost is $10 per KG (base), costPerBase = 10 / 1 = 10
    // If cost is $0.01 per Gram (factor 0.001), costPerBase = 0.01 / 0.001 = 10
    const costPerBase = baseCost / (ingredientUnit.conversion_factor || 1)
    
    // 2. Convert cost per base unit to cost per selected unit
    // If costPerBase is $10 per KG, and selected is Gram (0.001), unitCost = 10 * 0.001 = 0.01
    unitCost = costPerBase * (selectedUnit.conversion_factor || 1)
  }

  const wastage = parseFloat(item.wastage_percentage) || 0
  const grossQty = wastage < 100 ? (item.quantity / (1 - (wastage / 100))) : item.quantity
  
  return Number(unitCost * grossQty)
}

const onProductChange = () => {
  props.form.variant_id = ''
}

const onIngredientChange = (event, index) => {
  const ingredientId = event.target.value
  const ingredient = props.ingredients.find(ing => ing.id == ingredientId)
  if (ingredient) {
    props.form.items[index].unit_id = ingredient.unit_id
  }
}

const addItem = () => {
  props.form.items.push({
    ingredient_id: '',
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
