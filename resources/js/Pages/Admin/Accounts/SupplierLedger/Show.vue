<template>
  <AdminLayout :title="pageTitle">
    <div class="p-6">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ pageTitle }}</h1>
        <Link :href="route('admin.accounts.supplier-ledger.index')" class="text-indigo-600 hover:underline">&larr; Back to Suppliers</Link>
      </div>

      <!-- Statement Table -->
      <div class="bg-white rounded-lg shadow overflow-x-auto mb-6">
        <table class="w-full whitespace-nowrap">
          <thead class="bg-gray-50 border-b">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Debit</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Credit</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Balance</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="l in ledgers" :key="l.id">
              <td class="px-6 py-4">{{ l.transaction_date }}</td>
              <td class="px-6 py-4 capitalize">{{ l.reference_type }}</td>
              <td class="px-6 py-4">{{ l.notes }}</td>
              <td class="px-6 py-4 text-right text-green-600">{{ parseFloat(l.dr_amount).toFixed(2) }}</td>
              <td class="px-6 py-4 text-right text-red-600">{{ parseFloat(l.cr_amount).toFixed(2) }}</td>
              <td class="px-6 py-4 text-right font-bold">{{ parseFloat(l.balance).toFixed(2) }}</td>
            </tr>
            <tr v-if="ledgers.length === 0">
              <td colspan="6" class="px-6 py-4 text-center text-gray-500">No transactions found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Record Payment -->
      <div class="bg-white rounded-lg shadow p-6 max-w-lg">
        <h2 class="text-lg font-semibold mb-4">Record Payment</h2>
        <form @submit.prevent="submitPayment">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
            <input v-model="form.amount" type="number" step="0.01" class="w-full border rounded px-3 py-2" required />
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
            <input v-model="form.transaction_date" type="date" class="w-full border rounded px-3 py-2" required />
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
            <textarea v-model="form.notes" class="w-full border rounded px-3 py-2" rows="2"></textarea>
          </div>
          <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700" :disabled="form.processing">Save Payment</button>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ supplier: Object, ledgers: Array, pageTitle: String });

const form = useForm({ amount: '', transaction_date: '', notes: '' });

const submitPayment = () => {
    form.post(route('admin.accounts.supplier-ledger.payment', props.supplier.id), { preserveScroll: true });
};
</script>
