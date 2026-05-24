<template>
    <div class="space-y-6">
      <!-- Page Header -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ pageTitle }}</h1>
              <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Full debit/credit statement for <span class="font-bold">{{ supplier.name }}</span></p>
            </div>
            <Link :href="route('admin.accounts.supplier-ledger.index')"
                  class="px-3 py-1 bg-gray-100 text-gray-600 rounded text-sm hover:bg-gray-200 flex items-center space-x-1 transition">
              <span class="material-symbols-outlined text-sm">arrow_back</span>
              <span>Back</span>
            </Link>
          </div>
        </div>
      </div>

      <!-- Statement Table -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50 border-b border-gray-100">
              <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Date</th>
              <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Type</th>
              <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Notes</th>
              <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Debit ({{ currency() }})</th>
              <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Credit ({{ currency() }})</th>
              <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Balance ({{ currency() }})</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="l in ledgers" :key="l.id" class="hover:bg-gray-50/50 transition-colors">
              <td class="px-6 py-4 text-xs font-bold text-gray-800">{{ l.transaction_date }}</td>
              <td class="px-6 py-4">
                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full border capitalize"
                      :class="l.reference_type === 'payment' ? 'bg-green-50 text-green-600 border-green-100' : 'bg-amber-50 text-amber-600 border-amber-100'">
                  {{ l.reference_type }}
                </span>
              </td>
              <td class="px-6 py-4 text-xs text-gray-500 italic max-w-xs truncate">{{ l.notes || '-' }}</td>
              <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">
                {{ parseFloat(l.dr_amount) > 0 ? parseFloat(l.dr_amount).toLocaleString(undefined, {minimumFractionDigits: 2}) : '-' }}
              </td>
              <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">
                {{ parseFloat(l.cr_amount) > 0 ? parseFloat(l.cr_amount).toLocaleString(undefined, {minimumFractionDigits: 2}) : '-' }}
              </td>
              <td class="px-6 py-4 text-sm font-black text-right"
                  :class="parseFloat(l.balance) > 0 ? 'text-red-600' : 'text-emerald-600'">
                {{ currency() }}{{ parseFloat(l.balance).toLocaleString(undefined, {minimumFractionDigits: 2}) }}
              </td>
            </tr>
          </tbody>
          <tfoot v-if="ledgers.length > 0" class="bg-gray-50 font-black">
            <tr>
              <td colspan="3" class="px-6 py-4 text-xs uppercase tracking-widest text-gray-400">Closing Balance</td>
              <td class="px-6 py-4 text-right text-sm">{{ currency() }}{{ totalDebit.toLocaleString(undefined, {minimumFractionDigits: 2}) }}</td>
              <td class="px-6 py-4 text-right text-sm">{{ currency() }}{{ totalCredit.toLocaleString(undefined, {minimumFractionDigits: 2}) }}</td>
              <td class="px-6 py-4 text-right text-sm text-indigo-600">{{ currency() }}{{ closingBalance.toLocaleString(undefined, {minimumFractionDigits: 2}) }}</td>
            </tr>
          </tfoot>
        </table>

        <div v-if="ledgers.length === 0" class="p-20 text-center">
          <span class="material-symbols-outlined text-gray-200 text-6xl mb-4">receipt_long</span>
          <h3 class="text-gray-400 font-bold">No transactions recorded for this supplier</h3>
        </div>
      </div>

      <!-- Record Payment Form -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-emerald-50 to-white px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-bold text-gray-800">Record Payment</h2>
          <p class="text-gray-500 text-sm">Log a payment against this supplier's balance</p>
        </div>
        <div class="p-6">
          <form @submit.prevent="submitPayment" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
            <div class="space-y-1">
              <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Amount ({{ currency() }})</label>
              <input v-model="form.amount" type="number" step="0.01" required
                     class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500" />
            </div>
            <div class="space-y-1">
              <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Payment Date</label>
              <input v-model="form.transaction_date" type="date" required
                     class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500" />
            </div>
            <div class="space-y-1">
              <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Notes</label>
              <input v-model="form.notes" type="text" placeholder="Optional notes..."
                     class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500" />
            </div>
            <button type="submit"
                    class="px-6 py-2 bg-indigo-600 text-white rounded text-sm font-bold hover:bg-indigo-700 transition shadow-sm"
                    :disabled="form.processing">
              Save Payment
            </button>
          </form>
        </div>
      </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
defineOptions({ layout: AdminLayout })

const props = defineProps({ supplier: Object, ledgers: Array, pageTitle: String })

const form = useForm({ amount: '', transaction_date: '', notes: '' })

const submitPayment = () => {
    form.post(route('admin.accounts.supplier-ledger.payment', props.supplier.id), { preserveScroll: true })
}

const totalDebit = computed(() => props.ledgers.reduce((sum, l) => sum + (parseFloat(l.dr_amount) || 0), 0))
const totalCredit = computed(() => props.ledgers.reduce((sum, l) => sum + (parseFloat(l.cr_amount) || 0), 0))
const closingBalance = computed(() => props.ledgers.length > 0 ? parseFloat(props.ledgers[props.ledgers.length - 1].balance) : 0)
</script>
