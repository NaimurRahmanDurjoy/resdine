<template>
  <form @submit.prevent="$emit('submit', form)" class="space-y-8">

    <!-- Form Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 space-y-6">
      <!-- Name -->
      <div class="flex items-start gap-6">
        <label class="w-48 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">
          Full Name
        </label>
        <div class="flex-1 relative">
          <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 material-symbols-outlined">
            person
          </span>
          <input
            v-model="form.name"
            type="text"
            placeholder="Enter full name"
            class="pl-10 w-full h-10 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
            :class="{ 'border-red-500 focus:ring-red-500': form.errors.name }"
          />
          <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
        </div>
      </div>

      <!-- Email -->
      <div class="flex items-start gap-6">
        <label class="w-48 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">
          Email
        </label>
        <div class="flex-1 relative">
          <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 material-symbols-outlined">
            mail
          </span>
          <input
            v-model="form.email"
            type="email"
            placeholder="example@email.com"
            class="pl-10 w-full h-10 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
            :class="{ 'border-red-500 focus:ring-red-500': form.errors.email }"
          />
          <p v-if="form.errors.email" class="mt-1 text-sm text-red-500">{{ form.errors.email }}</p>
        </div>
      </div>

      <!-- Phone -->
      <div class="flex items-start gap-6">
        <label class="w-48 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">
          Phone
        </label>
        <div class="flex-1 relative">
          <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 material-symbols-outlined">
            call
          </span>
          <input
            v-model="form.phone"
            type="text"
            placeholder="01XXXXXXXXX"
            class="pl-10 w-full h-10 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
            :class="{ 'border-red-500 focus:ring-red-500': form.errors.phone }"
          />
          <p v-if="form.errors.phone" class="mt-1 text-sm text-red-500">{{ form.errors.phone }}</p>
        </div>
      </div>

      <!-- Role -->
      <div class="flex items-start gap-6">
        <label class="w-48 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">
          Role
        </label>
        <div class="flex-1">
          <select
            v-model="form.role_id"
            class="pl-3 w-full h-10 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
            :class="{ 'border-red-500 focus:ring-red-500': form.errors.role_id }"
          >
            <option value="">Select Role</option>
            <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
          </select>
          <p v-if="form.errors.role_id" class="mt-1 text-sm text-red-500">{{ form.errors.role_id }}</p>
        </div>
      </div>

      <!-- Status -->
      <div class="flex items-start gap-6">
        <label class="w-48 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">
          Status
        </label>
        <div class="flex-1">
          <select
            v-model="form.status"
            class="pl-3 w-full h-10 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
          >
            <option :value="1">Active</option>
            <option :value="0">Inactive</option>
          </select>
        </div>
      </div>

      <!-- Password -->
      <div class="flex items-start gap-6">
        <label class="w-48 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">
          Password
        </label>
        <div class="flex-1">
          <input
            v-model="form.password"
            type="password"
            placeholder="Enter password"
            class="pl-3 w-full h-10 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
            :class="{ 'border-red-500 focus:ring-red-500': form.errors.password }"
          />
          <p v-if="form.errors.password" class="mt-1 text-sm text-red-500">{{ form.errors.password }}</p>
        </div>
      </div>

      <!-- Confirm Password -->
      <div class="flex items-start gap-6">
        <label class="w-48 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">
          Confirm Password
        </label>
        <div class="flex-1">
          <input
            v-model="form.password_confirmation"
            type="password"
            placeholder="Confirm password"
            class="pl-3 w-full h-10 rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
          />
        </div>
      </div>

    </div>

    <!-- Form Buttons -->
    <div class="flex justify-start gap-3 border-gray-200 dark:border-gray-700">
      <Link :href="route('admin.users.index')"
        class="px-3 py-1 rounded text-sm border bg-red-400 text-white border-gray-300  hover:bg-red-500 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 transition"
      >
        Cancel
      </Link>
      <button
        type="submit"
        :disabled="form.processing"
        class="px-3 py-1 rounded text-sm bg-indigo-600 text-white font-medium shadow hover:bg-indigo-700 transition disabled:opacity-50"
      >
        {{ isEdit ? 'Update User' : 'Create User' }}
      </button>
    </div>

  </form>
</template>

<script setup>
const props = defineProps({
  form: Object,
  roles: Array,
  branches: Array,
  isEdit: { type: Boolean, default: false }
})

defineEmits(['submit'])
</script>
