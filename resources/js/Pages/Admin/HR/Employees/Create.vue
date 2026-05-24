<template>
    <div class="p-6">
      <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ pageTitle }}</h1>

      <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
        <form @submit.prevent="submit">
          <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Name</label>
              <input v-model="form.name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Email</label>
              <input v-model="form.email" type="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Phone</label>
              <input v-model="form.phone" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Employee Code</label>
              <input v-model="form.employee_code" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Department</label>
              <select v-model="form.staff_department_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Designation</label>
              <input v-model="form.designation" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Basic Salary</label>
              <input v-model="form.basic_salary" type="number" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Joining Date</label>
              <input v-model="form.joining_date" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
          </div>

          <div class="flex justify-end mt-6">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700" :disabled="form.processing">
              Save Employee
            </button>
          </div>
        </form>
      </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
defineOptions({ layout: AdminLayout })

defineProps({
    departments: Array,
    pageTitle: String
});

const form = useForm({
    name: '',
    email: '',
    phone: '',
    employee_code: '',
    staff_department_id: '',
    designation: '',
    basic_salary: 0,
    joining_date: ''
});

const submit = () => {
    form.post(route('admin.hr.employees.store'));
};
</script>
