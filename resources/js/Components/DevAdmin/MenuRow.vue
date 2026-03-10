<template>
  <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 group transition-colors"
      :class="level > 0 ? 'bg-gray-50/50 dark:bg-gray-900/30' : ''">
    <td :class="['px-4 py-2', level > 0 ? 'border-l-2 border-' + color + '-100 dark:border-' + color + '-900 text-gray-700 dark:text-gray-300' : 'font-bold text-' + color + '-900 dark:text-' + color + '-400']"
        :style="level > 0 ? `padding-left: ${(4 + level * 4) * 4}px` : ''">
      <div class="flex items-center gap-2">
        <span v-if="level > 0" :class="'text-' + color + '-300 leading-none'">└─</span>
        <span v-if="level === 0" :class="'w-1.5 h-1.5 rounded-full bg-' + color + '-500'"></span>
        {{ menu.name }}
      </div>
    </td>
    <td :class="['px-4 py-2 text-gray-600 dark:text-gray-400 font-mono text-xs', level > 0 ? 'text-gray-500 dark:text-gray-500' : '']">
      {{ menu.route || '-' }}</td>
    <td :class="['px-4 py-2', level > 0 ? '' : '']">
      <span v-if="menu.icon"
        :class="['material-symbols-outlined p-1.5 rounded-lg border', level > 0 ? 'text-gray-400 text-lg bg-gray-50 dark:bg-gray-800/30 border-gray-200 dark:border-gray-700/50' : 'text-' + color + '-500 bg-' + color + '-50 dark:bg-' + color + '-900/30 border-' + color + '-100 dark:border-' + color + '-800/50']">{{
          menu.icon }}</span>
      <span v-else class="text-gray-300">-</span>
    </td>
    <td :class="['px-4 py-2 text-xs italic', level > 0 ? 'text-gray-500' : 'text-gray-400']">{{ level > 0 ? parentName : 'Parent' }}</td>
    <td :class="['px-4 py-2', level > 0 ? '' : '']">
      <span :class="['px-2 py-0.5 border rounded text-[10px] font-bold text-gray-600 dark:text-gray-400', level > 0 ? 'bg-gray-100 dark:bg-gray-800 border-gray-200 dark:border-gray-700' : 'bg-gray-100 dark:bg-gray-800 border-gray-200 dark:border-gray-700']">
        ORDER: {{ menu.order }}
      </span>
    </td>
    <td :class="['px-4 py-2', level > 0 ? '' : '']">
      <span :class="['px-2 py-0.5 text-[10px] font-bold rounded-full uppercase tracking-tighter', menu.is_active ? 'bg-' + color + '-100 text-' + color + '-700 dark:bg-' + color + '-900/30 dark:text-' + color + '-400' : 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400']">
        {{ menu.is_active ? 'Active' : 'Inactive' }}
      </span>
    </td>
    <td :class="['px-4 py-2', level > 0 ? '' : '']">
      <div class="flex items-center gap-3">
        <Link :href="route(routePrefix + '.edit', menu.id)"
          :class="['p-1.5 rounded-md transition-colors tooltip', level > 0 ? 'text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' : 'text-' + color + '-600 hover:bg-' + color + '-50 dark:hover:bg-' + color + '-900/20']"
          :title="level > 0 ? 'Edit Child Menu' : 'Edit Menu'">
          <span class="material-symbols-outlined text-xl font-icon">edit</span>
        </Link>
        <button @click="deleteMenu(menu.id)"
          :class="['p-1.5 rounded-md transition-colors tooltip', level > 0 ? 'text-gray-400 hover:bg-red-100 dark:hover:bg-red-900/20' : 'text-' + color + '-600 hover:bg-' + color + '-50 dark:hover:bg-' + color + '-900/20']"
          :title="level > 0 ? 'Delete Child Menu' : 'Delete Menu'">
          <span class="material-symbols-outlined text-xl font-icon">delete</span>
        </button>
      </div>
    </td>
  </tr>

  <!-- Render children recursively -->
  <MenuRow v-for="child in menu.children_recursive" :key="child.id"
    :menu="child" :level="level + 1" :parent-name="menu.name" :color="color" :route-prefix="routePrefix" />
</template>

<script setup>
import { defineProps } from 'vue'
import { Link, router } from '@inertiajs/vue3'

const props = defineProps({
  menu: { type: Object, required: true },
  level: { type: Number, default: 0 },
  parentName: { type: String, default: '' },
  color: { type: String, default: 'indigo' }, // 'indigo' or 'emerald'
  routePrefix: { type: String, default: 'devAdmin.systemConfig.adminPanel.menu' }
})

const deleteMenu = (id) => {
  if (confirm('Are you sure you want to permanently delete this menu item and all its children?')) {
    router.delete(route(props.routePrefix + '.destroy', id), {
      onSuccess: () => {
        // Notification success
      }
    })
  }
}
</script>

<style scoped>
.font-icon {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
</style>