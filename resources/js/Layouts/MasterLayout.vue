<template>
  <div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-100 p-4">
      <h2 class="font-bold mb-4">Resdine</h2>
      <ul>
        <li><inertia-link href="/inertia-test">Dashboard</inertia-link></li>
        <li><inertia-link href="/users">Users</inertia-link></li>
        <li><inertia-link href="/orders">Orders</inertia-link></li>
      </ul>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-4">
      <slot />
    </main>
  </div>
</template>

<script setup>
import { Link as InertiaLink } from '@inertiajs/vue3'
</script>
