<template>
  <AdminLayout :title="pageTitle">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <!-- Page Header -->
      <div class="bg-white dark:bg-gray-800">
        <div
          class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Chart of Accounts</h1>
              <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Manage your restaurant's financial ledger</p>
            </div>
            <div class="flex gap-2">
              <button @click="postOpeningBalances"
                class="px-4 py-1.5 bg-gray-50 text-gray-600 rounded-md text-xs font-bold border border-gray-200 hover:bg-gray-100 transition flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">sync</span>
                Sync Opening Balances
              </button>
              <button @click="openCreateModal"
                class="px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700 flex items-center space-x-2 transition shadow-sm">
                <span class="material-symbols-outlined text-sm">add</span>
                <span>Add Account</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Accounts Table -->
      <ListTable :headers="headers" :items="accounts" :loading="loading">
        <template #rows="{ items }">
          <tr v-for="account in items" :key="account.id"
            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
            <td class="px-6 py-2 whitespace-nowrap text-sm font-mono text-gray-500">
              {{ account.code }}
            </td>
            <td class="px-6 py-2 whitespace-nowrap">
              <div class="flex items-center">
                <span v-if="account.parent_id" class="text-gray-300 mr-2">↳</span>
                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                  {{ account.name }}
                  <span v-if="account.is_system"
                    class="ml-2 text-[10px] text-indigo-500 font-bold uppercase tracking-tighter bg-indigo-50 px-1 rounded">System</span>
                </div>
              </div>
            </td>
            <td class="px-6 py-2 whitespace-nowrap">
              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                {{ account.account_type?.name }}
              </span>
            </td>
            <td class="px-6 py-2 whitespace-nowrap text-right text-sm text-gray-900 font-bold">
              ${{ parseFloat(account.opening_balance).toLocaleString() }}
            </td>
            <td class="px-6 py-2 whitespace-nowrap">
              <span :class="account.balance_type === 'D' ? 'text-blue-600' : 'text-green-600'"
                class="text-xs font-bold uppercase">
                {{ account.balance_type === 'D' ? 'Debit' : 'Credit' }}
              </span>
            </td>
            <td class="px-6 py-2 whitespace-nowrap text-right text-sm font-medium">
              <div class="flex justify-end space-x-2">
                <button @click="editAccount(account)" class="text-indigo-600 hover:text-indigo-900 transition-colors">
                  <span class="material-symbols-outlined text-sm">edit</span>
                </button>
                <button v-if="!account.is_system" @click="deleteAccount(account)"
                  class="text-red-600 hover:text-red-900 transition-colors">
                  <span class="material-symbols-outlined text-sm">delete</span>
                </button>
              </div>
            </td>
          </tr>
        </template>
      </ListTable>
    </div>

    <!-- Account Modal -->
    <Modal :show="showModal" @close="showModal = false"
      :title="editingAccount ? 'Edit Chart Of Account' : 'Add New Chart Of Account'">
      <AccountForm :account="editingAccount" :account-types="accountTypes" :parent-accounts="parentAccounts"
        @close="showModal = false" @success="onSuccess" />
    </Modal>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ListTable from '@/Components/ListTable.vue'
import Modal from '@/Components/Modal.vue'
import AccountForm from './AccountForm.vue'
import Swal from 'sweetalert2'

const props = defineProps({
  accounts: Array,
  accountTypes: Array,
  parentAccounts: Array,
  pageTitle: String
})

const loading = ref(false)
const showModal = ref(false)
const editingAccount = ref(null)

const headers = [
  { label: 'Code', key: 'code', sortable: false },
  { label: 'Name', key: 'name', sortable: false },
  { label: 'Type', key: 'type', sortable: false },
  { label: 'Opening Balance', key: 'opening', sortable: false },
  { label: 'Nature', key: 'nature', sortable: false },
  { label: 'Actions', key: 'actions', sortable: false }
]

const openCreateModal = () => {
  editingAccount.value = null
  showModal.value = true
}

const editAccount = (account) => {
  editingAccount.value = account
  showModal.value = true
}

const onSuccess = () => {
  showModal.value = false
  Swal.fire('Success', editingAccount.value ? 'Chart Of Account updated' : 'Chart Of Account created', 'success')
}

const deleteAccount = (account) => {
  Swal.fire({
    title: 'Are you sure?',
    text: "Deleting this account might affect transactions!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#4f46e5',
    confirmButtonText: 'Yes, delete it!'
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('admin.accounts.coa.destroy', account.id), {
        onSuccess: () => Swal.fire('Deleted!', 'Chart Of Account removed.', 'success')
      })
    }
  })
}

const postOpeningBalances = () => {
  Swal.fire({
    title: 'Sync Opening Balances?',
    text: "This will create a balanced journal entry for all accounts with starting balances.",
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#4f46e5',
    confirmButtonText: 'Yes, Sync Now'
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(route('admin.accounts.coa.post-opening'), {}, {
        onSuccess: () => Swal.fire('Synced!', 'Opening balances posted to ledger.', 'success')
      })
    }
  })
}
</script>
