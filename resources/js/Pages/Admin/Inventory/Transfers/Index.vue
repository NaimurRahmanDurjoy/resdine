<template>
  <AdminLayout :title="pageTitle">
    <div class="p-6">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ pageTitle }}</h1>
        <Link :href="route('admin.inventory.transfers.create')" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">New Transfer</Link>
      </div>
      <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full whitespace-nowrap">
          <thead class="bg-gray-50 border-b">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ref No</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">From</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">To</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="t in transfers.data" :key="t.id">
              <td class="px-6 py-4">{{ t.reference_no }}</td>
              <td class="px-6 py-4">{{ t.transfer_date }}</td>
              <td class="px-6 py-4">{{ t.from_branch?.name }}</td>
              <td class="px-6 py-4">{{ t.to_branch?.name }}</td>
              <td class="px-6 py-4">{{ t.status }}</td>
              <td class="px-6 py-4">
                <Link v-if="t.status === 'Pending'" :href="route('admin.inventory.transfers.dispatch', t.id)" method="post" as="button" class="text-indigo-600">Dispatch</Link>
                <Link v-if="t.status === 'Dispatched'" :href="route('admin.inventory.transfers.receive', t.id)" method="post" as="button" class="text-green-600">Receive</Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({ transfers: Object, pageTitle: String });
</script>
