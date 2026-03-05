<template>
  <form @submit.prevent="$emit('submit', form)" class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Name -->
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
        <input
          v-model="form.name"
          type="text"
          id="name"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300"
          :class="{ 'border-red-500': form.errors.name }"
        />
        <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</div>
      </div>

      <!-- Email -->
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
        <input
          v-model="form.email"
          type="email"
          id="email"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300"
          :class="{ 'border-red-500': form.errors.email }"
        />
        <div v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</div>
      </div>

      <!-- Phone -->
      <div>
        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
        <input
          v-model="form.phone"
          type="text"
          id="phone"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300"
          :class="{ 'border-red-500': form.errors.phone }"
        />
        <div v-if="form.errors.phone" class="mt-1 text-sm text-red-600">{{ form.errors.phone }}</div>
      </div>

      <!-- Role -->
      <div>
        <label for="role_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
        <select
          v-model="form.role_id"
          id="role_id"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300"
          :class="{ 'border-red-500': form.errors.role_id }"
        >
          <option value="">Select Role</option>
          <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
        </select>
        <div v-if="form.errors.role_id" class="mt-1 text-sm text-red-600">{{ form.errors.role_id }}</div>
      </div>

      <!-- Status -->
      <div>
        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
        <select
          v-model="form.status"
          id="status"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300"
          :class="{ 'border-red-500': form.errors.status }"
        >
          <option :value="1">Active</option>
          <option :value="0">Inactive</option>
        </select>
        <div v-if="form.errors.status" class="mt-1 text-sm text-red-600">{{ form.errors.status }}</div>
      </div>

      <!-- Password -->
      <div>
        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
          Password <span v-if="isEdit" class="text-xs text-gray-500">(Leave blank to keep current)</span>
        </label>
        <input
          v-model="form.password"
          type="password"
          id="password"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300"
          :class="{ 'border-red-500': form.errors.password }"
        />
        <div v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</div>
      </div>

      <!-- Password Confirmation -->
      <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
        <input
          v-model="form.password_confirmation"
          type="password"
          id="password_confirmation"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300"
        />
      </div>
    </div>

    <div class="flex justify-end space-x-3">
      <Link
        href="/admin/users"
        class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
      >
        Cancel
      </Link>
      <button
        type="submit"
        :disabled="form.processing"
        class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
      >
        {{ isEdit ? 'Update User' : 'Create User' }}
      </button>
    </div>
  </form>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  form: Object,
  roles: Array,
  branches: Array,
  isEdit: { type: Boolean, default: false }
})

defineEmits(['submit'])
</script>
