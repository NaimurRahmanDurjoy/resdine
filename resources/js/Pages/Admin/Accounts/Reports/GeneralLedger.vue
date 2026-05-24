<template>
    <div class="space-y-6">
      <!-- Page Header -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-50 to-white px-6 py-4 border-b border-gray-200">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-xl font-bold text-gray-800">General Ledger</h1>
              <p class="text-gray-600 text-sm mt-1">Detailed transaction history for individual accounts</p>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div class="p-6">
          <form @submit.prevent="applyFilter" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
            <div class="space-y-1">
              <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Select Account</label>
              <select v-model="form.account_id" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Choose an account...</option>
                <option v-for="acc in accounts" :key="acc.id" :value="acc.id">[{{ acc.code }}] {{ acc.name }}</option>
              </select>
            </div>
            <div class="space-y-1">
              <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">From Date</label>
              <input v-model="form.from_date" type="date" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="space-y-1">
              <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">To Date</label>
              <input v-model="form.to_date" type="date" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded text-sm font-bold hover:bg-indigo-700 transition shadow-sm">
              Generate Report
            </button>
          </form>
        </div>
      </div>

      <!-- Ledger Table -->
      <div v-if="filters.account_id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50 border-b border-gray-100">
              <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Date / Type</th>
              <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Voucher # / Reference</th>
              <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Narration</th>
              <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Debit ({{ currency() }})</th>
              <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Credit ({{ currency() }})</th>
              <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Balance ({{ currency() }})</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <!-- Opening Balance -->
            <tr class="bg-indigo-50/30">
              <td colspan="3" class="px-6 py-3 text-xs font-bold text-indigo-600 uppercase tracking-wider">Opening Balance as of {{ filters.from_date }}</td>
              <td></td>
              <td></td>
              <td class="px-6 py-3 text-right text-sm font-black text-indigo-700">
                {{ currency() }}{{ openingBalance.toLocaleString(undefined, {minimumFractionDigits: 2}) }}
              </td>
            </tr>
            <!-- Transactions -->
            <tr v-for="(row, index) in processedLedger" :key="index" class="hover:bg-gray-50/50 transition-colors">
              <td class="px-6 py-4">
                <div class="text-xs font-bold text-gray-800">{{ row.voucher.voucher_date }}</div>
                <div class="text-[10px] text-gray-400 uppercase">{{ row.voucher.voucher_type?.name }}</div>
              </td>
              <td class="px-6 py-4">
                <Link :href="route('admin.accounts.vouchers.show', row.voucher_id)" class="text-xs font-bold text-indigo-600 hover:underline">
                  {{ row.voucher.voucher_no }}
                </Link>
                <div class="text-[10px] text-gray-400">{{ row.voucher.reference_no || '-' }}</div>
              </td>
              <td class="px-6 py-4 text-xs text-gray-500 italic max-w-xs truncate">
                {{ row.narration || row.voucher.narration }}
              </td>
              <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">
                {{ row.debit > 0 ? parseFloat(row.debit).toLocaleString(undefined, {minimumFractionDigits: 2}) : '-' }}
              </td>
              <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">
                {{ row.credit > 0 ? parseFloat(row.credit).toLocaleString(undefined, {minimumFractionDigits: 2}) : '-' }}
              </td>
              <td class="px-6 py-4 text-sm font-black text-gray-900 text-right">
                {{ currency() }}{{ row.runningBalance.toLocaleString(undefined, {minimumFractionDigits: 2}) }}
              </td>
            </tr>
          </tbody>
          <tfoot v-if="processedLedger.length > 0" class="bg-gray-50 font-black">
            <tr>
              <td colspan="3" class="px-6 py-4 text-xs uppercase tracking-widest text-gray-400">Period Totals / Closing Balance</td>
              <td class="px-6 py-4 text-right text-sm">{{ currency() }}{{ periodTotalDebit.toLocaleString() }}</td>
              <td class="px-6 py-4 text-right text-sm">{{ currency() }}{{ periodTotalCredit.toLocaleString() }}</td>
              <td class="px-6 py-4 text-right text-sm text-indigo-600">{{ currency() }}{{ closingBalance.toLocaleString() }}</td>
            </tr>
          </tfoot>
        </table>
      </div>

      <div v-else class="bg-white rounded-xl shadow-sm border border-gray-100 p-20 text-center">
        <span class="material-symbols-outlined text-gray-200 text-6xl mb-4">analytics</span>
        <h3 class="text-gray-400 font-bold">Select an account to view the General Ledger</h3>
      </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
defineOptions({ layout: AdminLayout })

const props = defineProps({
  accounts: Array,
  ledger: Array,
  openingBalance: Number,
  filters: Object,
  pageTitle: String
})

const form = useForm({
  account_id: props.filters.account_id || '',
  from_date: props.filters.from_date || '',
  to_date: props.filters.to_date || ''
})

const processedLedger = computed(() => {
  let currentBalance = props.openingBalance
  return props.ledger.map(row => {
    // Logic: If Asset/Expense (Debit Nature), +Debit -Credit. If Liability/Revenue (Credit Nature), +Credit -Debit.
    // Simplification: We follow the account's base balance type.
    // For now, let's assume standard running balance math:
    // This is complex because we don't know the account nature here easily. 
    // We'll pass the nature or just use +Debit -Credit for now.
    currentBalance += (parseFloat(row.debit) || 0) - (parseFloat(row.credit) || 0)
    return { ...row, runningBalance: currentBalance }
  })
})

const periodTotalDebit = computed(() => props.ledger.reduce((sum, r) => sum + (parseFloat(r.debit) || 0), 0))
const periodTotalCredit = computed(() => props.ledger.reduce((sum, r) => sum + (parseFloat(r.credit) || 0), 0))
const closingBalance = computed(() => processedLedger.value.length > 0 ? processedLedger.value[processedLedger.value.length - 1].runningBalance : props.openingBalance)

const applyFilter = () => {
  form.get(route('admin.accounts.reports.ledger'), {
    preserveState: true,
    preserveScroll: true
  })
}
</script>
