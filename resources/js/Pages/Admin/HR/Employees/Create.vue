<template>
  <div
    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <!-- Page Header -->
    <div
      class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
      <div class="flex justify-between items-center">
        <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ pageTitle }}</h1>
        <Link :href="route('admin.hr.employees.index')"
          class="text-gray-500 hover:text-gray-700 flex items-center space-x-1">
          <span class="material-symbols-outlined text-sm">arrow_back</span>
          <span>Back</span>
        </Link>
      </div>
    </div>

    <!-- Form Content -->
    <form @submit.prevent="submit" class="p-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6">
        <div class="space-y-2">
          <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Name</label>
          <input v-model="form.name" type="text"
            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
            required>
        </div>
        <div class="space-y-2">
          <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Email</label>
          <input v-model="form.email" type="email"
            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
            required>
        </div>
        <div class="space-y-2">
          <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Phone</label>
          <input v-model="form.phone" type="text"
            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
        </div>
        <div class="space-y-2">
          <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Employee Code</label>
          <input v-model="form.employee_code" type="text"
            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
            required>
        </div>
        <div class="space-y-2">
          <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Department</label>
          <select v-model="form.staff_department_id"
            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
            required>
            <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
          </select>
        </div>
        <div class="space-y-2">
          <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Designation</label>
          <input v-model="form.designation" type="text"
            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
            required>
        </div>
        <div class="space-y-2">
  <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
    Role
  </label>

          <select v-model="form.role_id"
            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" required>
            <option value="">Select Role</option>
            <option v-for="role in roles" :key="role.id" :value="role.id">
              {{ role.name }}
            </option>
          </select>
        </div>
        <div class="space-y-2">
          <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Basic Salary</label>
          <input v-model="form.basic_salary" type="number" step="0.01"
            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
            required>
        </div>
        <div class="space-y-2">
          <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Joining Date</label>
          <input v-model="form.joining_date" type="date"
            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
            required>
        </div>
      </div>

      <div class="mt-6 flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
        <Link :href="route('admin.hr.employees.index')"
          class="px-6 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition mr-3">
          Cancel
        </Link>
        <button type="submit" :disabled="form.processing"
          class="px-8 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 disabled:opacity-50 transition shadow-md flex items-center gap-2">
          <span v-if="form.processing" class="animate-spin material-symbols-outlined text-sm">sync</span>
          <span>Save Employee</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
defineOptions({ layout: AdminLayout })

defineProps({
  departments: Array,
  roles: Array,
  pageTitle: String
});

const form = useForm({
  name: '',
  email: '',
  phone: '',
  employee_code: '',
  staff_department_id: '',
  role_id: '',
  designation: '',
  basic_salary: 0,
  joining_date: ''
});

const submit = () => {
  form.post(route('admin.hr.employees.store'));
};
</script>
