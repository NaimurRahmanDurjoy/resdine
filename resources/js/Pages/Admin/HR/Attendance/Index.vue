<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-800">
      <div
        class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ pageTitle }} - {{ date }}</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Record attendance for employees and view the daily
              log.</p>
          </div>
        </div>

      </div>
    </div>

    <div class="p-6 space-y-6">
      <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Mark Attendance</h2>
        <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Employee</label>
            <select v-model="form.employee_id"
              class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
              required>
              <option value="">Select Employee</option>
              <option v-for="employee in employees" :key="employee.id" :value="employee.id">
                {{ employee.employee_code }} - {{ employee.user?.name }}
              </option>
            </select>
          </div>
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Date</label>
            <input v-model="form.date" type="date"
              class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
              required>
          </div>
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Status</label>
            <select v-model="form.status"
              class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
              required>
              <option value="">Select Status</option>
              <option>Present</option>
              <option>Absent</option>
              <option>Late</option>
              <option>Half-day</option>
            </select>
          </div>
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Check In</label>
            <input v-model="form.check_in" type="time"
              class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
          </div>
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Check Out</label>
            <input v-model="form.check_out" type="time"
              class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
          </div>
          <div class="flex items-end justify-end">
            <button type="submit" :disabled="form.processing"
              class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 disabled:opacity-50 transition shadow-md flex items-center justify-center gap-2">
              <span v-if="form.processing" class="animate-spin material-symbols-outlined text-sm">sync</span>
              <span>Save Attendance</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <ListTable :headers="headers" :items="attendances.data" :pagination="attendances" :loading="false">
      <template #rows="{ items }">
        <tr v-for="att in items" :key="att.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
          <td class="px-6 py-2 whitespace-nowrap">
            <div class="text-sm font-medium text-gray-900">{{ att.employee?.user?.name }}</div>
          </td>
          <td class="px-6 py-2 whitespace-nowrap">
            <div class="text-sm text-gray-600">{{ att.check_in || '--:--' }}</div>
          </td>
          <td class="px-6 py-2 whitespace-nowrap">
            <div class="text-sm text-gray-600">{{ att.check_out || '--:--' }}</div>
          </td>
          <td class="px-6 py-2 whitespace-nowrap">
            <div class="text-sm text-gray-900 font-bold">{{ att.status }}</div>
          </td>
        </tr>
      </template>
      <template #pagination v-if="attendances.links">
        <div class="flex items-center justify-between w-full">
          <div class="text-sm text-gray-700 dark:text-gray-400">
            Showing <span class="font-medium">{{ attendances.from }}</span> to <span class="font-medium">{{
              attendances.to }}</span> of <span class="font-medium">{{ attendances.total }}</span> entries
          </div>
          <div class="flex space-x-1">
            <Link v-for="(link, k) in attendances.links" :key="k" :href="link.url || '#'" v-html="link.label"
              class="relative inline-flex items-center px-3 py-1 border text-xs font-medium transition-colors rounded shadow-sm"
              :class="[link.active ? 'z-10 bg-indigo-600 border-indigo-600 text-white' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700', !link.url ? 'cursor-not-allowed opacity-50' : '']" />
          </div>
        </div>
      </template>
    </ListTable>
  </div>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ListTable from '@/Components/ListTable.vue';
defineOptions({ layout: AdminLayout })

defineProps({
  attendances: Object,
  employees: Array,
  date: String,
  pageTitle: String
});

const form = useForm({
  employee_id: '',
  date: new Date().toISOString().slice(0, 10),
  status: '',
  check_in: '',
  check_out: ''
});

const submit = () => {
  form.post(route('admin.hr.attendance.mark'), { preserveScroll: true });
};

const headers = [
  { label: 'Employee', key: 'employee', sortable: false },
  { label: 'Check In', key: 'check_in', sortable: false },
  { label: 'Check Out', key: 'check_out', sortable: false },
  { label: 'Status', key: 'status', sortable: false }
];
</script>
