<template>
  <div class="h-screen flex items-center justify-center bg-gray-950 font-sans p-6 overflow-y-auto">
    <div class="w-full max-w-lg bg-gray-900 rounded-3xl shadow-2xl border border-white/5 overflow-hidden p-8">
      <div class="text-center mb-10">
        <div class="inline-flex p-4 bg-red-500/10 rounded-2xl mb-4 border border-red-500/20">
          <span class="material-symbols-outlined text-4xl text-red-400">lock</span>
        </div>
        <h1 class="text-3xl font-black text-white tracking-tight uppercase italic">Close Register</h1>
        <p class="text-gray-400 font-bold text-xs uppercase tracking-widest mt-2">Reconcile shift and end session</p>
      </div>

      <div class="grid grid-cols-2 gap-4 mb-8">
        <div class="p-4 bg-gray-800/50 rounded-2xl border border-white/5">
          <div class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Opening Cash</div>
          <div class="text-xl font-black text-white">${{ register.opening_cash }}</div>
        </div>
        <div class="p-4 bg-indigo-500/10 rounded-2xl border border-indigo-500/10">
          <div class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-1">Expected Cash</div>
          <div class="text-xl font-black text-white">${{ register.expected_cash }}</div>
        </div>
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <div>
          <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Actual Counted Cash ($)</label>
          <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-500 material-symbols-outlined">calculate</span>
            <input v-model="form.closing_cash" type="number" step="0.01" required
              class="w-full bg-gray-800 border-white/5 rounded-2xl py-4 pl-12 pr-4 text-white font-black text-2xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all placeholder-gray-700"
              placeholder="0.00">
          </div>
        </div>

        <div v-if="difference !== 0" class="p-4 rounded-2xl flex items-center gap-3 transition-all"
          :class="difference > 0 ? 'bg-green-500/10 text-green-400 border border-green-500/10' : 'bg-red-500/10 text-red-400 border border-red-500/10'">
          <span class="material-symbols-outlined">{{ difference > 0 ? 'add_circle' : 'remove_circle' }}</span>
          <div>
            <div class="text-[10px] font-black uppercase tracking-widest">Discrepancy Detected</div>
            <div class="text-lg font-black">${{ Math.abs(difference).toFixed(2) }} {{ difference > 0 ? 'Over' : 'Short' }}</div>
          </div>
        </div>

        <div>
          <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Shift Notes / Explanations</label>
          <textarea v-model="form.notes"
            class="w-full bg-gray-800 border-white/5 rounded-2xl py-4 px-4 text-white font-medium text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all h-24"
            placeholder="e.g. Paid for supply run from till, or reason for discrepancy..."></textarea>
        </div>

        <div class="flex gap-4">
          <Link :href="route('admin.pos.index')" class="flex-1 py-4 bg-gray-800 text-gray-400 rounded-2xl font-black text-sm uppercase tracking-widest text-center hover:bg-gray-700 transition-all">Cancel</Link>
          <button type="submit" :disabled="form.processing"
            class="flex-[2] py-4 bg-red-600 hover:bg-red-500 text-white rounded-2xl font-black text-sm uppercase tracking-widest transition-all shadow-lg shadow-red-500/20 flex items-center justify-center gap-2 active:scale-95 disabled:opacity-50">
            <span class="material-symbols-outlined text-lg">close_fullscreen</span>
            Close & Reconcile
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
  register: Object
});

const form = useForm({
  closing_cash: '',
  notes: ''
});

const difference = computed(() => {
  if (!form.closing_cash) return 0;
  return parseFloat(form.closing_cash) - parseFloat(props.register.expected_cash);
});

const submit = () => {
  form.post(route('admin.pos.register.close.submit'));
};
</script>
