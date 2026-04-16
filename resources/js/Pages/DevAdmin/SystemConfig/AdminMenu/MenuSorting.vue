<template>
    <DevAdminLayout>
        <!-- Modern Container -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-indigo-50 to-white dark:from-indigo-900/20 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h1 class="text-xl font-bold text-gray-800 dark:text-white">Admin Menu Sorting</h1>
                <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Drag items to reorder or nest them under others.</p>
            </div>

            <!-- Menu List -->
            <div class="p-6">
                <draggable
                    v-model="menus"
                    item-key="id"
                    group="menus"
                    :animation="200"
                    ghost-class="bg-indigo-50 dark:bg-indigo-900/50"
                    handle=".drag-handle"
                    class="space-y-2"
                    @change="onMenusChanged"
                    tag="div">
                    <template #item="{ element }">
                        <MenuSortingItem
                            :menu="element"
                            :color="'indigo'"
                            :route-prefix="'devAdmin.systemConfig.adminPanel.menu'"
                            @update:menu="updateMenu"
                        />
                    </template>
                </draggable>

                <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-4">
                    <button
                        @click="saveOrder"
                        :disabled="isSaving"
                        class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed transition">
                        <span v-if="!isSaving">Save Order</span>
                        <span v-else class="flex items-center gap-2">
                            <span class="material-symbols-outlined animate-spin text-lg">hourglass_empty</span>
                            Saving...
                        </span>
                    </button>
                    <span
                        v-if="message"
                        :class="['ml-4 text-sm', messageType === 'success' ? 'text-emerald-600' : 'text-rose-600']">
                        {{ message }}
                    </span>
                </div>
            </div>
        </div>
    </DevAdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'        
import draggable from 'vuedraggable'
import DevAdminLayout from '@/Layouts/DevAdminLayout.vue'
import MenuSortingItem from '@/Components/DevAdmin/MenuSortingItem.vue'

const props = defineProps({
    items: Array
})

const normalizeMenus = (items) => {
    return items.map(item => ({
        ...item,
        children: normalizeMenus(item.children || item.children_recursive || [])
    }))
}

const menus = ref(normalizeMenus(props.items || []))
const message = ref('')
const messageType = ref('success')
const isSaving = ref(false)

const dragOptions = {
    animation: 200,
    group: 'menus',
    ghostClass: 'bg-indigo-50 dark:bg-indigo-900/50',
    handle: '.drag-handle'
}

const onMenusChanged = () => {
    message.value = 'Changes made - click Save to update'
    messageType.value = 'success'
}

const updateMenu = (updatedMenu) => {
    const findAndUpdate = (items) => {
        for (let i = 0; i < items.length; i++) {
            if (items[i].id === updatedMenu.id) {
                items[i] = updatedMenu
                return
            }
            if (items[i].children && items[i].children.length > 0) {
                findAndUpdate(items[i].children)
            }
        }
    }
    findAndUpdate(menus.value)
}

const getMenuStructure = (items) => {
    return items.map((item, index) => ({
        id: item.id,
        order: index + 1,
        children: item.children && item.children.length > 0 ? getMenuStructure(item.children) : []
    }))
}

const saveOrder = () => {
    isSaving.value = true

    const structure = getMenuStructure(menus.value)

    router.post(
        route('devAdmin.systemConfig.adminPanel.menuSorting.updateOrder'),
        { structure },
        {
            preserveScroll: true,

            onSuccess: () => {
                message.value = 'Order saved successfully!'
                messageType.value = 'success'

                setTimeout(() => {
                    message.value = ''
                }, 3000)
            },

            onError: () => {
                message.value = 'Error saving order'
                messageType.value = 'error'
            },

            onFinish: () => {
                isSaving.value = false
            }
        }
    )
}
</script>

<style scoped>
.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
</style>
