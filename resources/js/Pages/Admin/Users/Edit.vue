<template>
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-800">
      <div class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Update User</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Fill in the details below to update the user : {{ user.name }}</p>
          </div>
          <Link :href="route('admin.users.index')" class="flex items-center text-sm text-indigo-600 hover:text-indigo-900 transition-colors">
            <span class="material-symbols-outlined mr-1 text-base">arrow_back</span>
            Back to Users
          </Link>
        </div>
      </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700 p-6">
      <UserForm 
        :form="form" 
        :roles="roles" 
        :branches="branches" 
        :is-edit="true"
        @submit="submit" 
      />
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import UserForm from './Form.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  user: Object,
  roles: Array,
  branches: Array
})

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  phone: props.user.phone || '',
  role_id: props.user.role_id,
  status: props.user.status,
  password: '',
  password_confirmation: ''
})

const submit = () => {
  form.put(route('admin.users.update', props.user.id), {
    onSuccess: () => form.reset('password', 'password_confirmation')
  })
}
</script>
