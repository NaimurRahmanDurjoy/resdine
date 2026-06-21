<template>
    <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div
            class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ pageTitle }}</h1>
                <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Create a leave request for an employee.</p>
            </div>
            <Link :href="route('admin.hr.leaves.index')"
                class="px-3 py-1 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 rounded text-sm hover:bg-gray-50 dark:hover:bg-gray-800 transition flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
                <span>Back</span>
            </Link>
        </div>

        <form @submit.prevent="submit" class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Leave Type</label>
                    <input v-model="form.type" type="text"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
                        placeholder="e.g. Sick Leave" required>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Start Date</label>
                    <input v-model="form.start_date" type="date"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
                        required>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">End Date</label>
                    <input v-model="form.end_date" type="date"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
                        required>
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Reason</label>
                <textarea v-model="form.reason" rows="4"
                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
                    placeholder="Provide a short reason for the leave request" required></textarea>
            </div>

            <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" :disabled="form.processing"
                    class="px-8 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 disabled:opacity-50 transition shadow-md flex items-center gap-2">
                    <span v-if="form.processing" class="animate-spin material-symbols-outlined text-sm">sync</span>
                    <span>Submit Request</span>
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
    employees: Array,
    pageTitle: String
});

const form = useForm({
    employee_id: '',
    start_date: '',
    end_date: '',
    type: '',
    reason: ''
});

const submit = () => {
    form.post(route('admin.hr.leaves.store'), { preserveScroll: true });
};
</script>
