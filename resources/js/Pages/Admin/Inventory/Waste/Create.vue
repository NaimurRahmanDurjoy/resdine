<template>
  <AdminLayout :title="pageTitle">
    <div class="p-6">
      <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ pageTitle }}</h1>
      <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
        <form @submit.prevent="submit">
          <!-- Form fields for item, branch, qty, reason, date go here -->
          <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Save Log</button>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({ items: Array, branches: Array, pageTitle: String });

const form = useForm({ inventory_item_id: '', branch_id: '', quantity: '', reason: '', date: '', notes: '' });

const submit = () => form.post(route('admin.inventory.waste.store'));
</script>
