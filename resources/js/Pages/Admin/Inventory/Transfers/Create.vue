<template>
  <AdminLayout :title="pageTitle">
    <div class="p-6">
      <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ pageTitle }}</h1>
      <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
        <form @submit.prevent="submit">
          <!-- Form fields go here -->
          <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Initiate Transfer</button>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({ items: Array, branches: Array, pageTitle: String });

const form = useForm({ from_branch_id: '', to_branch_id: '', transfer_date: '', items: [] });
const submit = () => form.post(route('admin.inventory.transfers.store'));
</script>
