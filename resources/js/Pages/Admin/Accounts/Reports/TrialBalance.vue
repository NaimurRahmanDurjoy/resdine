<template>
    <div class="space-y-6">
      <!-- Page Header -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-50 to-white px-6 py-4 border-b border-gray-200">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-xl font-bold text-gray-800">Trial Balance</h1>
              <p class="text-gray-600 text-sm mt-1">Snapshot of all account balances for audit verification</p>
            </div>
            <button @click="window.print()" class="px-4 py-2 bg-white text-gray-600 rounded-md text-xs font-bold border border-gray-200 hover:bg-gray-50 transition flex items-center gap-2 shadow-sm">
              <span class="material-symbols-outlined text-sm">print</span>
              Print Report
            </button>
          </div>
        </div>

        <!-- Filters -->
        <div class="p-6">
          <form @submit.prevent="applyFilter" class="flex gap-4 items-end">
            <div class="space-y-1">
              <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">As Of Date</label>
              <input v-model="form.as_of_date" type="date" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded text-sm font-bold hover:bg-indigo-700 transition shadow-sm">
              Generate Trial Balance
            </button>
          </form>
        </div>
      </div>

      <!-- Trial Balance Table -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden print:shadow-none print:border-0">
        <div class="p-6 bg-gray-50/30 border-b border-gray-100 hidden print:block">
           <h2 class="text-xl font-bold text-center uppercase tracking-widest text-gray-800">Trial Balance</h2>
           <p class="text-center text-gray-500 text-sm">As of {{ filters.as_of_date }}</p>
        </div>
        
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50 border-b border-gray-100">
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Account Code & Name</th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Account Type</th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right w-48">Debit ({{ currency() }})</th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right w-48">Credit ({{ currency() }})</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="account in data" :key="account.id" class="hover:bg-gray-50/50 transition-colors" v-show="account.debit !== 0 || account.credit !== 0">
              <td class="px-8 py-3">
                <div class="text-sm font-bold text-gray-800">{{ account.code }} - {{ account.name }}</div>
              </td>
              <td class="px-8 py-3">
                <span class="text-[10px] font-black uppercase text-gray-400 px-2 py-0.5 bg-gray-100 rounded border border-gray-200">
                  {{ account.type }}
                </span>
              </td>
              <td class="px-8 py-3 text-sm font-black text-gray-900 text-right">
                {{ account.debit !== 0 ? account.debit.toLocaleString(undefined, {minimumFractionDigits: 2}) : '-' }}
              </td>
              <td class="px-8 py-3 text-sm font-black text-gray-900 text-right">
                {{ account.credit !== 0 ? account.credit.toLocaleString(undefined, {minimumFractionDigits: 2}) : '-' }}
              </td>
            </tr>
          </tbody>
          <tfoot class="bg-indigo-600 text-white font-black">
            <tr>
              <td colspan="2" class="px-8 py-4 text-sm uppercase tracking-widest">Total Trial Balance</td>
              <td class="px-8 py-4 text-right text-lg">{{ currency() }}{{ totalDebit.toLocaleString(undefined, {minimumFractionDigits: 2}) }}</td>
              <td class="px-8 py-4 text-right text-lg">{{ currency() }}{{ totalCredit.toLocaleString(undefined, {minimumFractionDigits: 2}) }}</td>
            </tr>
          </tfoot>
        </table>

        <!-- Validation Message -->
        <div v-if="Math.abs(totalDebit - totalCredit) > 0.01" class="p-4 bg-red-50 border-t border-red-100 text-center">
           <p class="text-red-600 font-bold text-sm">Warning: Trial Balance is NOT equal! Difference: {{ currency() }}{{ Math.abs(totalDebit - totalCredit).toFixed(2) }}</p>
        </div>
        <div v-else class="p-4 bg-green-50 border-t border-green-100 text-center">
           <p class="text-green-600 font-bold text-sm">The Trial Balance is perfectly balanced.</p>
        </div>
      </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
defineOptions({ layout: AdminLayout })

const props = defineProps({
  data: Array,
  filters: Object,
  pageTitle: String
})

const form = useForm({
  as_of_date: props.filters.as_of_date || ''
})

const totalDebit = computed(() => props.data.reduce((sum, r) => sum + r.debit, 0))
const totalCredit = computed(() => props.data.reduce((sum, r) => sum + r.credit, 0))

const applyFilter = () => {
  form.get(route('admin.accounts.reports.trial-balance'), {
    preserveState: true,
    preserveScroll: true
  })
}
</script>
