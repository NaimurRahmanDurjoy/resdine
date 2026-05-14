<template>
  <div class="h-screen flex items-center justify-center bg-gray-950 font-sans p-6">
    <div class="w-full max-w-md bg-gray-900 rounded-3xl shadow-2xl border border-white/5 overflow-hidden p-8">
      <div class="text-center mb-10">
        <div class="inline-flex p-4 bg-indigo-500/10 rounded-2xl mb-4 border border-indigo-500/20">
          <span class="material-symbols-outlined text-4xl text-indigo-400">lock_open</span>
        </div>
        <h1 class="text-3xl font-black text-white tracking-tight uppercase italic">Open Register</h1>
        <p class="text-gray-400 font-bold text-xs uppercase tracking-widest mt-2">Initialize your shift cash balance</p>
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <div>
          <label class="block text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-2 ml-1">Opening Cash Amount ({{ currency() }})</label>
          <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-500 material-symbols-outlined">payments</span>
            <input v-model="form.opening_cash" type="number" step="0.01" required
              class="w-full bg-gray-800 border-white/5 rounded-2xl py-4 pl-12 pr-4 text-white font-black text-2xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all placeholder-gray-700"
              placeholder="0.00">
          </div>
        </div>

        <button type="submit" :disabled="form.processing"
          class="w-full py-4 bg-indigo-600 hover:bg-indigo-500 text-white rounded-2xl font-black text-sm uppercase tracking-widest transition-all shadow-lg shadow-indigo-500/20 flex items-center justify-center gap-2 active:scale-95 disabled:opacity-50">
          <span class="material-symbols-outlined text-lg">start</span>
          Start Shift
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
  opening_cash: ''
});

const submit = () => {
  form.post(route('admin.pos.register.open.submit'));
};
</script>
