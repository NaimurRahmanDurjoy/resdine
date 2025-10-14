@extends('layouts.devAdmin')

@section('title', 'Dashboard')
@section('page-title', 'Developer Dashboard')
@section('page-description', 'System overview and quick actions')

@section('content')
<div class="space-y-6">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-blue-100 dark:bg-blue-900">
                    <i class="fas fa-users text-blue-600 dark:text-blue-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Users</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">1,248</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-green-100 dark:bg-green-900">
                    <i class="fas fa-database text-green-600 dark:text-green-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Database Size</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">245 MB</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-yellow-100 dark:bg-yellow-900">
                    <i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Errors Today</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">12</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-purple-100 dark:bg-purple-900">
                    <i class="fas fa-server text-purple-600 dark:text-purple-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Server Load</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">42%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- System Health -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">System Health</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Cache</span>
                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Healthy</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Database</span>
                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Connected</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Queue</span>
                    <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">Processing</span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('admin.system.cache') }}" 
                   class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-bolt text-blue-600 mr-3"></i>
                    <span class="text-sm font-medium">Clear Cache</span>
                </a>
                <a href="{{ route('admin.system.logs') }}" 
                   class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-file-alt text-green-600 mr-3"></i>
                    <span class="text-sm font-medium">View Logs</span>
                </a>
                <a href="{{ route('admin.database') }}" 
                   class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-database text-purple-600 mr-3"></i>
                    <span class="text-sm font-medium">Database</span>
                </a>
                <a href="{{ route('admin.users.create') }}" 
                   class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-plus text-red-600 mr-3"></i>
                    <span class="text-sm font-medium">Add User</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection