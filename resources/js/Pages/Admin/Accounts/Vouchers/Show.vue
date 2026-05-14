<template>
  <AdminLayout :title="pageTitle">
    <div class="max-w-4xl mx-auto space-y-6">
      <!-- Header / Actions -->
      <div class="flex justify-between items-center bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-4">
          <Link :href="route('admin.accounts.vouchers.index')" class="text-gray-400 hover:text-indigo-600 transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
          </Link>
          <div>
            <h1 class="text-xl font-bold text-gray-800 tracking-tight">Voucher #{{ voucher.voucher_no }}</h1>
            <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mt-0.5">{{ voucher.voucher_type?.name }}</p>
          </div>
        </div>
        <div class="flex gap-2">
           <button v-if="voucher.status === 1" @click="approveVoucher" class="px-4 py-1.5 bg-green-600 text-white rounded-md text-xs font-bold hover:bg-green-700 flex items-center gap-2 shadow-sm transition-all">
             <span class="material-symbols-outlined text-sm">check_circle</span>
             Approve & Post
           </button>
           <button @click="window.print()" class="px-4 py-1.5 bg-gray-50 text-gray-600 rounded-md text-xs font-bold hover:bg-gray-100 flex items-center gap-2 border border-gray-200 transition-all">
             <span class="material-symbols-outlined text-sm">print</span>
             Print
           </button>
        </div>
      </div>

      <!-- Main Voucher Card -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden print:shadow-none print:border-0">
        <div class="p-8 border-b border-gray-50 bg-gray-50/30 grid grid-cols-3 gap-8">
           <div>
             <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Voucher Date</label>
             <div class="text-sm font-bold text-gray-800">{{ voucher.voucher_date }}</div>
           </div>
           <div>
             <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Reference No</label>
             <div class="text-sm font-bold text-gray-800">{{ voucher.reference_no || '---' }}</div>
           </div>
           <div>
             <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Status</label>
             <div class="inline-flex">
               <span v-if="voucher.status === 1" class="px-2 py-0.5 bg-yellow-50 text-yellow-600 text-[10px] font-bold rounded-full border border-yellow-100 uppercase">Draft</span>
               <span v-else-if="voucher.status === 2" class="px-2 py-0.5 bg-green-50 text-green-600 text-[10px] font-bold rounded-full border border-green-100 uppercase">Approved</span>
               <span v-else class="px-2 py-0.5 bg-red-50 text-red-600 text-[10px] font-bold rounded-full border border-red-100 uppercase">Cancelled</span>
             </div>
           </div>
        </div>

        <div class="p-0">
           <table class="w-full text-left border-collapse">
             <thead>
               <tr class="bg-white border-b border-gray-100">
                 <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Account Head & Memo</th>
                 <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right w-40">Debit ({{ currency() }})</th>
                 <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right w-40">Credit ({{ currency() }})</th>
               </tr>
             </thead>
             <tbody class="divide-y divide-gray-50">
               <tr v-for="detail in voucher.details" :key="detail.id" class="hover:bg-gray-50/50 transition-colors">
                 <td class="px-8 py-4">
                   <div class="text-sm font-bold text-gray-800">[{{ detail.account?.code }}] {{ detail.account?.name }}</div>
                   <div v-if="detail.narration" class="text-[11px] text-gray-400 mt-0.5 italic">{{ detail.narration }}</div>
                 </td>
                 <td class="px-8 py-4 text-sm font-black text-gray-900 text-right">
                   {{ detail.debit > 0 ? parseFloat(detail.debit).toLocaleString(undefined, {minimumFractionDigits: 2}) : '-' }}
                 </td>
                 <td class="px-8 py-4 text-sm font-black text-gray-900 text-right">
                   {{ detail.credit > 0 ? parseFloat(detail.credit).toLocaleString(undefined, {minimumFractionDigits: 2}) : '-' }}
                 </td>
               </tr>
             </tbody>
             <tfoot class="bg-indigo-50/20 font-black">
               <tr>
                 <td class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Voucher Amount</td>
                 <td class="px-8 py-4 text-sm text-indigo-600 text-right font-black border-t-2 border-indigo-100">
                   {{ currency() }}{{ parseFloat(voucher.total_debit).toLocaleString(undefined, {minimumFractionDigits: 2}) }}
                 </td>
                 <td class="px-8 py-4 text-sm text-indigo-600 text-right font-black border-t-2 border-indigo-100">
                   {{ currency() }}{{ parseFloat(voucher.total_credit).toLocaleString(undefined, {minimumFractionDigits: 2}) }}
                 </td>
               </tr>
             </tfoot>
           </table>
        </div>

        <div class="p-8 bg-gray-50/50 border-t border-gray-100">
           <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Narration / Internal Note</label>
           <p class="text-sm text-gray-600 leading-relaxed italic">{{ voucher.narration || 'No narration provided for this voucher.' }}</p>
        </div>
      </div>

      <!-- Audit & Source Information -->
      <div class="grid grid-cols-2 gap-6 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
         <div class="space-y-4">
            <div>
               <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Prepared By</label>
               <div class="text-sm font-bold text-gray-700">{{ voucher.creator?.name || 'System Generated' }}</div>
               <div class="text-[10px] text-gray-400">{{ voucher.created_at_formatted }}</div>
            </div>
            <div v-if="voucher.approved_by">
               <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Approved By</label>
               <div class="text-sm font-bold text-gray-700">{{ voucher.approver?.name }}</div>
               <div class="text-[10px] text-gray-400">{{ voucher.approved_at_formatted }}</div>
            </div>
         </div>
         
         <div v-if="voucher.sources?.length > 0">
            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Source Documents</label>
            <div class="space-y-2">
               <div v-for="source in voucher.sources" :key="source.id" class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-100">
                  <span class="material-symbols-outlined text-indigo-500">link</span>
                  <div>
                     <div class="text-xs font-bold text-gray-800">{{ source.source_type }}</div>
                     <Link v-if="source.source_type === 'Order'" :href="route('admin.pos.orders.show', source.source_id)" class="text-[10px] text-indigo-600 hover:underline font-bold">
                        View Original #{{ source.source_id }}
                     </Link>
                     <div v-else class="text-[10px] text-gray-400 font-bold">ID: {{ source.source_id }}</div>
                  </div>
               </div>
            </div>
         </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Swal from 'sweetalert2'

const props = defineProps({
  voucher: Object,
  pageTitle: String
})

const approveVoucher = () => {
  Swal.fire({
    title: 'Post to Ledger?',
    text: "This will officially record the transaction and lock the voucher.",
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#059669',
    confirmButtonText: 'Yes, Approve & Post'
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(route('admin.accounts.vouchers.approve', props.voucher.id), {}, {
        onSuccess: () => Swal.fire('Success', 'Voucher posted to ledger.', 'success')
      })
    }
  })
}
</script>

<style scoped>
@media print {
  body * {
    visibility: hidden;
  }
  .print-area, .print-area * {
    visibility: visible;
  }
  .print-area {
    position: absolute;
    left: 0;
    top: 0;
  }
}
</style>
