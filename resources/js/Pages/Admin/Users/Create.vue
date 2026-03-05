<template>
  <div class="space-y-6">
    <div class="mb-6">
      <Link href="/admin/users" class="flex items-center text-sm text-indigo-600 hover:text-indigo-900 transition-colors">
        <span class="material-symbols-outlined mr-1 text-base">arrow_back</span>
        Back to Users
      </Link>
      <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">Create New User</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-100 dark:border-gray-700 p-6">
      <UserForm 
        :form="form" 
        :roles="roles" 
        :branches="branches" 
        @submit="submit" 
      />
    </div>
  </div>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import UserForm from './Form.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  roles: Array,
  branches: Array
})

const form = useForm({
  name: '',
  email: '',
  phone: '',
  role_id: '',
  status: 1,
  password: '',
  password_confirmation: ''
})

const submit = () => {
  form.post('/admin/users', {
    onSuccess: () => form.reset('password', 'password_confirmation')
  })
}
</script>
