<template>
    <div class="space-y-6">
      <!-- Page Header -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-50 to-white px-6 py-4 border-b border-gray-200">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-xl font-bold text-gray-800">Profit & Loss Statement</h1>
              <p class="text-gray-600 text-sm mt-1">Summary of revenues and expenses to determine net income</p>
            </div>
            <button @click="window.print()"
              class="px-4 py-2 bg-white text-gray-600 rounded-md text-xs font-bold border border-gray-200 hover:bg-gray-50 transition flex items-center gap-2 shadow-sm">
              <span class="material-symbols-outlined text-sm">print</span>
              Print Statement
            </button>
          </div>
        </div>

        <!-- Filters -->
        <div class="p-6">
          <form @submit.prevent="applyFilter" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
            <div class="space-y-1">
              <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">From Date</label>
              <input v-model="form.from_date" type="date"
                class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="space-y-1">
              <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">To Date</label>
              <input v-model="form.to_date" type="date"
                class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <button type="submit"
              class="px-6 py-2 bg-indigo-600 text-white rounded text-sm font-bold hover:bg-indigo-700 transition shadow-sm">
              Generate P&L
            </button>
          </form>
        </div>
      </div>

      <!-- P&L Statement -->
      <div
        class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden max-w-6xl mx-auto print:max-w-full print:border-0 print:shadow-none">
        <div class="p-10 border-b border-gray-50 bg-gray-50/30 text-center">
          <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tighter">Profit & Loss Statement</h2>
          <p class="text-gray-500 text-sm font-bold tracking-widest mt-1 uppercase">{{ filters.from_date }} TO {{
            filters.to_date }}</p>
        </div>

        <div class="p-10 space-y-12">
          <!-- Revenues -->
          <section>
            <h3
              class="text-xs font-black text-indigo-600 uppercase tracking-widest border-b-2 border-indigo-100 pb-2 mb-4 flex justify-between">
              <span>Operating Revenues</span>
              <span>Amount ({{ currency() }})</span>
            </h3>
            <div class="space-y-3">
              <div v-for="acc in revenueAccounts" :key="acc.id"
                class="flex justify-between text-sm font-medium text-gray-700">
                <span>{{ acc.name }}</span>
                <span class="font-bold">{{ acc.balance.toLocaleString(undefined, { minimumFractionDigits: 2 }) }}</span>
              </div>
              <div v-if="revenueAccounts.length === 0" class="text-gray-400 text-xs italic">No revenue recorded in this
                period.</div>
            </div>
            <div class="mt-6 pt-4 border-t border-gray-100 flex justify-between items-center">
              <span class="text-sm font-black text-gray-800 uppercase">Total Revenue</span>
              <span class="text-lg font-black text-gray-900 border-b-2 border-gray-900 pb-1">{{ currency() }}{{
                totalRevenue.toLocaleString(undefined, { minimumFractionDigits: 2 }) }}</span>
            </div>
          </section>

          <!-- Expenses -->
          <section>
            <h3
              class="text-xs font-black text-red-500 uppercase tracking-widest border-b-2 border-red-100 pb-2 mb-4 flex justify-between">
              <span>Operating Expenses</span>
              <span>Amount ({{ currency() }})</span>
            </h3>
            <div class="space-y-3">
              <div v-for="acc in expenseAccounts" :key="acc.id"
                class="flex justify-between text-sm font-medium text-gray-700">
                <span>{{ acc.name }}</span>
                <span class="font-bold">({{ acc.balance.toLocaleString(undefined, { minimumFractionDigits: 2 })
                  }})</span>
              </div>
              <div v-if="expenseAccounts.length === 0" class="text-gray-400 text-xs italic">No expenses recorded in this
                period.</div>
            </div>
            <div class="mt-6 pt-4 border-t border-gray-100 flex justify-between items-center">
              <span class="text-sm font-black text-gray-800 uppercase">Total Expenses</span>
              <span class="text-lg font-black text-gray-900 border-b-2 border-gray-900 pb-1">{{ currency() }}{{
                totalExpense.toLocaleString(undefined, { minimumFractionDigits: 2 }) }}</span>
            </div>
          </section>

          <!-- Summary / Net Profit -->
          <div class="bg-indigo-600 rounded-xl p-8 text-white flex justify-between items-center shadow-lg">
            <div>
              <h4 class="text-xs font-black uppercase tracking-widest opacity-70">Bottom Line Performance</h4>
              <p class="text-2xl font-black uppercase tracking-tighter mt-1">
                {{ netProfit >= 0 ? 'Net Profit' : 'Net Loss' }}</p>
            </div>
            <div class="text-right">
              <span class="text-4xl font-black tracking-tighter">{{ currency() }}{{ netProfit.toLocaleString(undefined,
                { minimumFractionDigits: 2 }) }}</span>
              <p class="text-[10px] font-bold uppercase tracking-widest mt-1 opacity-70">Accrual Basis Accounting</p>
            </div>
          </div>
        </div>

        <!-- Footer / Signature -->
        <div class="p-10 border-t border-gray-50 flex justify-between items-center bg-gray-50/30">
          <div class="text-[10px] text-gray-400 font-bold uppercase italic">Report generated on {{ new
            Date().toLocaleString() }}</div>
          <div
            class="w-48 border-t border-gray-300 pt-2 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
            Authorized Signature</div>
        </div>
      </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
defineOptions({ layout: AdminLayout })

const props = defineProps({
  revenueAccounts: Array,
  expenseAccounts: Array,
  totalRevenue: Number,
  totalExpense: Number,
  netProfit: Number,
  filters: Object,
  pageTitle: String
})

const form = useForm({
  from_date: props.filters.from_date || '',
  to_date: props.filters.to_date || ''
})

const applyFilter = () => {
  form.get(route('admin.accounts.reports.profit-loss'), {
    preserveState: true,
    preserveScroll: true
  })
}
</script>
