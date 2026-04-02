<template>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Page Header -->
        <div class="bg-white dark:bg-gray-800">
            <div
                class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">My Profile</h1>
                        <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Update your basic details, profile
                            photo, and password.</p>
                    </div>
                    <Link :href="route('admin.dashboard')"
                        class="flex items-center text-sm text-indigo-600 hover:text-indigo-900 transition-colors">
                        <span class="material-symbols-outlined mr-1 text-base">arrow_back</span>
                        Back to Dashboard
                    </Link>
                </div>
            </div>
        </div>

        <div v-if="flash.success" class="p-4 text-green-800 bg-green-100 rounded-lg">{{ flash.success }}</div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 p-8">
            <!-- Personal Details -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-6 flex items-center gap-3">
                    <span class="w-1 h-6 bg-indigo-500 dark:bg-indigo-400 rounded"></span>
                    <span class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-indigo-500 dark:text-indigo-400">person</span>
                        Personal Details
                    </span>
                </h2>

                <form @submit.prevent="submitProfile" class="space-y-4" enctype="multipart/form-data">
                    <!-- Profile Photo -->
                    <div class="flex items-start gap-4">
                        <ImageUploadPreview size="w-48 h-48" shape="rounded-full" name="profile_photo"
                            :existingImage="user.profile?.profile_photo" @change="onPhotoSelected" />
                    </div>

                    <!-- Name -->
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4">
                        <label class="w-48 text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                        <div class="flex-1 flex flex-col">
                            <input v-model="profileForm.name" type="text"
                                class="h-10 pl-3 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                :class="{ 'border-red-500 focus:ring-red-500': profileForm.errors.name }" />
                            <p v-if="profileForm.errors.name" class="text-red-500 text-sm mt-1">{{
                                profileForm.errors.name }}</p>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4">
                        <label class="w-48 text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <div class="flex-1 flex flex-col relative">
                            <span
                                class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 material-symbols-outlined">
                                mail
                            </span>
                            <input v-model="profileForm.email" type="email"
                                class="h-10 pl-10 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                :class="{ 'border-red-500 focus:ring-red-500': profileForm.errors.email }" />
                            <p v-if="profileForm.errors.email" class="text-red-500 text-sm mt-1">{{
                                profileForm.errors.email }}</p>
                        </div>
                    </div>

                    <!-- Phone -->
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4">
                        <label class="w-48 text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                        <div class="flex-1 flex flex-col">
                            <input v-model="profileForm.phone" type="text"
                                class="h-10 pl-3 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                :class="{ 'border-red-500 focus:ring-red-500': profileForm.errors.phone }" />
                            <p v-if="profileForm.errors.phone" class="text-red-500 text-sm mt-1">{{
                                profileForm.errors.phone }}</p>
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4">
                        <label class="w-48 text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
                        <div class="flex-1 flex flex-col">
                            <textarea v-model="profileForm.address" rows="3"
                                class="pl-3 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition resize-none"></textarea>
                            <p v-if="profileForm.errors.address" class="text-red-500 text-sm mt-1">{{
                                profileForm.errors.address }}</p>
                        </div>
                    </div>

                    <!-- Date of Birth & Gender -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Date of Birth</label>
                            <input v-model="profileForm.dob" type="date"
                                class="h-10 pl-3 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" />
                            <p v-if="profileForm.errors.dob" class="text-red-500 text-sm mt-1">{{ profileForm.errors.dob
                                }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Gender</label>
                            <select v-model="profileForm.gender"
                                class="h-10 pl-3 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                                <option value="">Select</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                            <p v-if="profileForm.errors.gender" class="text-red-500 text-sm mt-1">{{
                                profileForm.errors.gender }}</p>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex justify-end">
                        <button type="submit" :disabled="profileForm.processing"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50">
                            Save Info
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-6 flex items-center gap-3">
                    <span class="w-1 h-6 bg-green-500 dark:bg-green-400 rounded"></span>
                    <span class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-green-500 dark:text-green-400">lock</span>
                        Change Password
                    </span>
                </h2>
                <form @submit.prevent="submitPassword" class="mt-15 space-y-4">
                    <div class="space-y-6">
                        <!-- Current Password -->
                        <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4">
                            <label class="w-48 text-sm font-medium text-gray-700 dark:text-gray-300">Current Password</label>
                            <div class="flex-1 flex flex-col relative">
                                <input :type="showCurrentPassword ? 'text' : 'password'"
                                    v-model="passwordForm.current_password"
                                    class="h-10 pl-3 pr-10 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                                    :class="{ 'border-red-500 focus:ring-red-500': passwordForm.errors.current_password }" />
                                <span @click="showCurrentPassword = !showCurrentPassword"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer text-gray-400 hover:text-gray-600 material-symbols-outlined">
                                    {{ showCurrentPassword ? 'visibility_off' : 'visibility' }}
                                </span>
                                <p v-if="passwordForm.errors.current_password" class="text-red-500 text-sm mt-1">{{
                                    passwordForm.errors.current_password }}</p>
                            </div>
                        </div>

                        <!-- New Password -->
                        <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4">
                            <label class="w-48 text-sm font-medium text-gray-700 dark:text-gray-300">New Password</label>
                            <div class="flex-1 flex flex-col relative">
                                <input :type="showNewPassword ? 'text' : 'password'"
                                    v-model="passwordForm.password"
                                    class="h-10 pl-3 pr-10 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                                    :class="{ 'border-red-500 focus:ring-red-500': passwordForm.errors.password }" />
                                <span @click="showNewPassword = !showNewPassword"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer text-gray-400 hover:text-gray-600 material-symbols-outlined">
                                    {{ showNewPassword ? 'visibility_off' : 'visibility' }}
                                </span>
                                <p v-if="passwordForm.errors.password" class="text-red-500 text-sm mt-1">{{
                                    passwordForm.errors.password }}</p>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4">
                            <label class="w-48 text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
                            <div class="flex-1 flex flex-col relative">
                                <input :type="showConfirmPassword ? 'text' : 'password'"
                                    v-model="passwordForm.password_confirmation"
                                    class="h-10 pl-3 pr-10 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                                    :class="{ 'border-red-500 focus:ring-red-500': passwordForm.errors.password_confirmation }" />
                                <span @click="showConfirmPassword = !showConfirmPassword"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer text-gray-400 hover:text-gray-600 material-symbols-outlined">
                                    {{ showConfirmPassword ? 'visibility_off' : 'visibility' }}
                                </span>
                                <p v-if="passwordForm.errors.password_confirmation" class="text-red-500 text-sm mt-1">{{
                                    passwordForm.errors.password_confirmation }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" :disabled="passwordForm.processing"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ImageUploadPreview from '@/Components/ImageUploadPreview.vue'
import { ref } from 'vue'

const showCurrentPassword = ref(false)
const showNewPassword = ref(false)
const showConfirmPassword = ref(false)

const props = defineProps({
    user: Object,
})

defineOptions({ layout: AdminLayout })

// Safe flash access
const flash = usePage()?.props?.flash || {};

// Profile form
const profileForm = useForm({
    _method: 'PUT',
    name: props.user.name || '',
    email: props.user.email || '',
    phone: props.user.phone || '',
    address: props.user.profile?.address || '',
    dob: props.user.profile?.dob || '',
    gender: props.user.profile?.gender || '',
    profile_photo: null,
})

// Password form
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
})

function onPhotoSelected(event) {
    profileForm.profile_photo = event.target.files?.[0] || null
}

function submitProfile() {
    profileForm.post(route('admin.profile.update'), {
        forceFormData: true,
        onSuccess: () => {
            profileForm.reset('profile_photo')
        }
    })
}

function submitPassword() {
    passwordForm.put(route('admin.profile.password.update'), {
        onSuccess: () => passwordForm.reset('current_password', 'password', 'password_confirmation')
    })
}
</script>
