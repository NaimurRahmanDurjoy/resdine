<template>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden max-w-6xl mx-auto">
      <!-- Page Header -->
      <div class="bg-white dark:bg-gray-800">
        <div class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <div class="flex items-center gap-4">
             <Link :href="route('admin.accounts.vouchers.index')" class="text-gray-400 hover:text-indigo-600 transition-colors">
               <span class="material-symbols-outlined">arrow_back</span>
             </Link>
             <div>
               <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">New Voucher Entry</h1>
               <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Manual double-entry posting to ledger</p>
             </div>
          </div>
        </div>
      </div>

      <form @submit.prevent="submit" class="p-6 space-y-6">
        <!-- Master Info -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-gray-50/50 p-6 rounded-xl border border-gray-100">
          <div class="space-y-1">
            <label class="text-xs font-bold text-gray-700 uppercase">Voucher Type</label>
            <select v-model="form.voucher_type_id" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
              <option v-for="type in voucherTypes" :key="type.id" :value="type.id">{{ type.name }}</option>
            </select>
          </div>
          <div class="space-y-1">
            <label class="text-xs font-bold text-gray-700 uppercase">Voucher Date</label>
            <input v-model="form.voucher_date" type="date" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
          </div>
          <div class="space-y-1">
            <label class="text-xs font-bold text-gray-700 uppercase">Reference #</label>
            <input v-model="form.reference_no" type="text" placeholder="Bill # or Memo" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
          </div>
          <div class="md:col-span-3 space-y-1">
            <label class="text-xs font-bold text-gray-700 uppercase">Narration</label>
            <textarea v-model="form.narration" rows="2" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="General description of this transaction..."></textarea>
          </div>
        </div>

        <!-- Ledger Entries -->
        <div class="border border-gray-100 rounded-xl overflow-hidden">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-gray-50 border-b border-gray-100">
                <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase">Account Head</th>
                <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase text-right w-32">Debit ({{ currency() }})</th>
                <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase text-right w-32">Credit ({{ currency() }})</th>
                <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase">Row Memo</th>
                <th class="px-4 py-3 w-12"></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="(row, index) in form.details" :key="index">
                <td class="p-2">
                  <select v-model="row.chart_of_account_id" class="block w-full px-2 py-1 border-0 bg-transparent text-sm font-medium focus:ring-0">
                    <option value="" disabled>Select Account</option>
                    <option v-for="acc in accounts" :key="acc.id" :value="acc.id">[{{ acc.code }}] {{ acc.name }}</option>
                  </select>
                </td>
                <td class="p-2">
                  <input v-model.number="row.debit" @input="row.credit = 0" type="number" step="0.01" class="block w-full px-2 py-1 border-0 bg-transparent text-sm font-bold text-right focus:ring-0">
                </td>
                <td class="p-2">
                  <input v-model.number="row.credit" @input="row.debit = 0" type="number" step="0.01" class="block w-full px-2 py-1 border-0 bg-transparent text-sm font-bold text-right focus:ring-0">
                </td>
                <td class="p-2">
                  <input v-model="row.narration" type="text" class="block w-full px-2 py-1 border-0 bg-transparent text-xs text-gray-500 focus:ring-0" placeholder="...">
                </td>
                <td class="p-2 text-center">
                  <button type="button" @click="removeRow(index)" class="text-gray-300 hover:text-red-500">
                    <span class="material-symbols-outlined text-sm">delete</span>
                  </button>
                </td>
              </tr>
            </tbody>
            <tfoot class="bg-gray-50/50 font-bold border-t border-gray-100">
              <tr>
                <td class="px-4 py-3">
                  <button type="button" @click="addRow" class="text-indigo-600 text-xs flex items-center gap-1 hover:underline">
                    <span class="material-symbols-outlined text-sm">add_circle</span> Add Row
                  </button>
                </td>
                <td class="px-4 py-3 text-right text-indigo-600 font-black">{{ currency() }}{{ totalDebit.toFixed(2) }}</td>
                <td class="px-4 py-3 text-right text-indigo-600 font-black">{{ currency() }}{{ totalCredit.toFixed(2) }}</td>
                <td colspan="2" class="px-4 py-3 text-right">
                  <span v-if="difference !== 0" class="text-red-500 text-[10px] uppercase italic font-bold">Unbalanced: {{ currency() }}{{ Math.abs(difference).toFixed(2) }}</span>
                  <span v-else class="text-green-600 text-[10px] uppercase font-bold">Balanced</span>
                </td>
              </tr>
            </tfoot>
          </table>
        </div>

        <div class="flex justify-end pt-4 border-t">
          <button type="submit" :disabled="form.processing || difference !== 0 || totalDebit === 0" class="px-10 py-2 bg-indigo-600 text-white rounded text-sm font-bold hover:bg-indigo-700 disabled:opacity-50 transition shadow-sm">
            Save & Post Voucher
          </button>
        </div>
      </form>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Swal from 'sweetalert2'
defineOptions({ layout: AdminLayout })

const props = defineProps({
  voucherTypes: Array,
  accounts: Array,
  pageTitle: String
})

const form = useForm({
  voucher_type_id: 1,
  voucher_date: new Date().toISOString().substr(0, 10),
  reference_no: '',
  narration: '',
  details: [
    { chart_of_account_id: '', debit: 0, credit: 0, narration: '' },
    { chart_of_account_id: '', debit: 0, credit: 0, narration: '' }
  ]
})

const totalDebit = computed(() => form.details.reduce((sum, row) => sum + (parseFloat(row.debit) || 0), 0))
const totalCredit = computed(() => form.details.reduce((sum, row) => sum + (parseFloat(row.credit) || 0), 0))
const difference = computed(() => totalDebit.value - totalCredit.value)

const addRow = () => form.details.push({ chart_of_account_id: '', debit: 0, credit: 0, narration: '' })
const removeRow = (index) => form.details.length > 2 ? form.details.splice(index, 1) : Swal.fire('Error', 'Journal entries must have at least 2 lines.', 'error')

const submit = () => {
  form.post(route('admin.accounts.vouchers.store'), {
    onSuccess: () => {
      Swal.fire({
        title: 'Posted!',
        text: 'Voucher has been saved and posted to ledger.',
        icon: 'success',
        timer: 2000,
        showConfirmButton: false
      })
    }
  })
}
</script>
