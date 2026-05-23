<template>
  <AdminLayout :title="pageTitle">
    <div class="p-6">
      <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ pageTitle }}</h1>
      <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full whitespace-nowrap">
          <thead class="bg-gray-50 border-b">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Company</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Balance</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="s in suppliers.data" :key="s.id">
              <td class="px-6 py-4 font-medium">{{ s.name }}</td>
              <td class="px-6 py-4">{{ s.company_name }}</td>
              <td class="px-6 py-4 text-right font-bold" :class="s.current_balance > 0 ? 'text-red-600' : 'text-green-600'">{{ parseFloat(s.current_balance || 0).toFixed(2) }}</td>
              <td class="px-6 py-4">
                <Link :href="route('admin.accounts.supplier-ledger.show', s.id)" class="text-indigo-600 hover:underline">View Statement</Link>
              </td>
            </tr>
            <tr v-if="!suppliers.data || suppliers.data.length === 0">
              <td colspan="4" class="px-6 py-4 text-center text-gray-500">No supplier data available.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({ suppliers: Object, pageTitle: String });
</script>
