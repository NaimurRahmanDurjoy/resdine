<template>
  <form @submit.prevent="submit" class="p-6 space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="space-y-1">
        <label class="text-xs font-bold text-gray-700 uppercase">Account Code</label>
        <input v-model="form.code" type="text" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. 1001">
        <div v-if="form.errors.code" class="text-red-500 text-[10px] font-bold">{{ form.errors.code }}</div>
      </div>
      <div class="space-y-1">
        <label class="text-xs font-bold text-gray-700 uppercase">Account Name</label>
        <input v-model="form.name" type="text" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. Cash in Hand">
        <div v-if="form.errors.name" class="text-red-500 text-[10px] font-bold">{{ form.errors.name }}</div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="space-y-1">
        <label class="text-xs font-bold text-gray-700 uppercase">Account Type</label>
        <select v-model="form.account_type_id" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
          <option v-for="type in accountTypes" :key="type.id" :value="type.id">{{ type.name }}</option>
        </select>
      </div>
      <div class="space-y-1">
        <label class="text-xs font-bold text-gray-700 uppercase">Parent Account</label>
        <select v-model="form.parent_id" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
          <option :value="null">No Parent</option>
          <option v-for="acc in parentAccounts" :key="acc.id" :value="acc.id">[{{ acc.code }}] {{ acc.name }}</option>
        </select>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="space-y-1">
        <label class="text-xs font-bold text-gray-700 uppercase">Opening Balance</label>
        <input v-model="form.opening_balance" type="number" step="0.01" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
      </div>
      <div class="space-y-1">
        <label class="text-xs font-bold text-gray-700 uppercase">Balance Nature</label>
        <select v-model="form.balance_type" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
          <option value="D">Debit</option>
          <option value="C">Credit</option>
        </select>
      </div>
    </div>

    <div class="flex items-center gap-2 py-1">
      <input type="checkbox" v-model="form.allow_transaction" id="allow_trans_part" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
      <label for="allow_trans_part" class="text-xs font-bold text-gray-600">Allow Transactions</label>
    </div>

    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
      <button type="button" @click="$emit('close')" class="px-4 py-2 text-sm text-gray-500 font-bold hover:text-gray-700">Cancel</button>
      <button type="submit" :disabled="form.processing" class="px-6 py-2 bg-indigo-600 text-white rounded text-sm font-bold hover:bg-indigo-700 shadow-sm transition-all">
        {{ account ? 'Update Account' : 'Save Account' }}
      </button>
    </div>
  </form>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { onMounted } from 'vue'

const props = defineProps({
  account: { type: Object, default: null },
  accountTypes: Array,
  parentAccounts: Array
})

const emit = defineEmits(['close', 'success'])

const form = useForm({
  code: '',
  name: '',
  account_type_id: 1,
  parent_id: null,
  opening_balance: 0,
  balance_type: 'D',
  allow_transaction: true,
  description: ''
})

onMounted(() => {
  if (props.account) {
    form.code = props.account.code
    form.name = props.account.name
    form.account_type_id = props.account.account_type_id
    form.parent_id = props.account.parent_id
    form.opening_balance = props.account.opening_balance
    form.balance_type = props.account.balance_type
    form.allow_transaction = !!props.account.allow_transaction
    form.description = props.account.description
  }
})

const submit = () => {
  if (props.account) {
    form.put(route('admin.accounts.coa.update', props.account.id), {
      onSuccess: () => emit('success')
    })
  } else {
    form.post(route('admin.accounts.coa.store'), {
      onSuccess: () => emit('success')
    })
  }
}
</script>
