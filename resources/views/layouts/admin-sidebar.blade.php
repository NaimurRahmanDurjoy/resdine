<aside class="fixed inset-y-0 left-0 z-30 w-64 bg-white dark:bg-gray-800 shadow-lg transform lg:translate-x-0 transition-transform duration-300 ease-in-out"
       :class="{ '-translate-x-full': !sidebarOpen }"
       x-cloak>
    
    <!-- Logo -->
    <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-code text-white text-sm"></i>
            </div>
            <span class="text-xl font-bold text-gray-800 dark:text-white">DevAdmin</span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="mt-6 px-4">
        <div class="space-y-2">
            <!-- Dashboard -->
            <a href="{{ route('devAdmin.dashboard') }}" 
               class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 dark:bg-gray-700 text-blue-600 dark:text-blue-400' : '' }}">
                <i class="fas fa-tachometer-alt w-5 mr-3"></i>
                <span>Dashboard</span>
            </a>

            <!-- User Management -->
            <div x-data="{ open: {{ request()->routeIs('admin.users.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" 
                        class="flex items-center justify-between w-full px-4 py-3 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="flex items-center">
                        <i class="fas fa-users w-5 mr-3"></i>
                        <span>User Management</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
                </button>
                
                <div x-show="open" x-collapse class="ml-4 mt-2 space-y-1">
                    <a href="{{ route('admin.users.index') }}" 
                       class="flex items-center px-4 py-2 text-sm text-gray-600 dark:text-gray-300 rounded hover:bg-blue-50 dark:hover:bg-gray-700 {{ request()->routeIs('admin.users.index') ? 'text-blue-600 dark:text-blue-400' : '' }}">
                        <i class="fas fa-list w-4 mr-2"></i>
                        All Users
                    </a>
                    <a href="{{ route('admin.users.create') }}" 
                       class="flex items-center px-4 py-2 text-sm text-gray-600 dark:text-gray-300 rounded hover:bg-blue-50 dark:hover:bg-gray-700 {{ request()->routeIs('admin.users.create') ? 'text-blue-600 dark:text-blue-400' : '' }}">
                        <i class="fas fa-plus w-4 mr-2"></i>
                        Add User
                    </a>
                </div>
            </div>

            <!-- System Tools -->
            <div x-data="{ open: {{ request()->routeIs('admin.system.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" 
                        class="flex items-center justify-between w-full px-4 py-3 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="flex items-center">
                        <i class="fas fa-cogs w-5 mr-3"></i>
                        <span>System Tools</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
                </button>
                
                <div x-show="open" x-collapse class="ml-4 mt-2 space-y-1">
                    <a href="#" 
                       class="flex items-center px-4 py-2 text-sm text-gray-600 dark:text-gray-300 rounded hover:bg-blue-50 dark:hover:bg-gray-700">
                        <i class="fas fa-file-alt w-4 mr-2"></i>
                        Logs
                    </a>
                    <a href="#" 
                       class="flex items-center px-4 py-2 text-sm text-gray-600 dark:text-gray-300 rounded hover:bg-blue-50 dark:hover:bg-gray-700">
                        <i class="fas fa-tasks w-4 mr-2"></i>
                        Queue Monitor
                    </a>
                    <a href="#" 
                       class="flex items-center px-4 py-2 text-sm text-gray-600 dark:text-gray-300 rounded hover:bg-blue-50 dark:hover:bg-gray-700">
                        <i class="fas fa-bolt w-4 mr-2"></i>
                        Cache Management
                    </a>
                </div>
            </div>

            <!-- Database -->
            <a href="#" 
               class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-database w-5 mr-3"></i>
                <span>Database</span>
            </a>

            <!-- Settings -->
            <a href="#" 
               class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-cog w-5 mr-3"></i>
                <span>Settings</span>
            </a>
        </div>
    </nav>

    <!-- Bottom Info -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200 dark:border-gray-700">
        <div class="text-xs text-gray-500 dark:text-gray-400">
            <div>v{{ config('app.version', '1.0.0') }}</div>
            <div>Env: {{ app()->environment() }}</div>
        </div>
    </div>
</aside>