@extends('layouts.devAdmin')

@section('title', 'Dashboard')
@section('page-title', 'Developer Dashboard')
@section('page-description', 'System overview and quick actions')

@section('content')
<div class="space-y-6">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-blue-100 dark:bg-blue-900">
                    <i class="fas fa-users text-blue-600 dark:text-blue-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Users</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($totalUsers) }}</p>
                </div>
            </div>
        </div>

        <!-- Database Size -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-green-100 dark:bg-green-900">
                    <i class="fas fa-database text-green-600 dark:text-green-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Database Size</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $databaseSize }} MB</p>
                </div>
            </div>
        </div>

        <!-- Errors -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-yellow-100 dark:bg-yellow-900">
                    <i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Errors Today</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $errorsToday }}</p>
                </div>
            </div>
        </div>

        <!-- Server Load -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-purple-100 dark:bg-purple-900">
                    <i class="fas fa-server text-purple-600 dark:text-purple-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Server Load</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $serverLoad }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- System Health & Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">System Health</h3>
            <div class="space-y-4">
                @foreach($systemHealth as $key => $status)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400 capitalize">{{ $key }}</span>
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($status === 'Healthy' || $status === 'Connected') bg-green-100 text-green-800 
                            @elseif($status === 'Processing') bg-yellow-100 text-yellow-800 
                            @else bg-red-100 text-red-800 @endif">
                            {{ $status }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('devAdmin.cache.clear') }}" class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-bolt text-blue-600 mr-3"></i>
                    <span class="text-sm font-medium">Clear Cache</span>
                </a>
                <a href="{{ route('devAdmin.logs.view') }}" class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-file-alt text-green-600 mr-3"></i>
                    <span class="text-sm font-medium">View Logs</span>
                </a>
                <a href="{{ route('devAdmin.database.info') }}" class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-database text-purple-600 mr-3"></i>
                    <span class="text-sm font-medium">Database</span>
                </a>
                <a href="{{ route('devAdmin.users.create') }}" class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-plus text-red-600 mr-3"></i>
                    <span class="text-sm font-medium">Add User</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
