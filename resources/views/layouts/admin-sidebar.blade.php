<!-- Material Icons CDN -->
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

<aside class="fixed inset-y-0 left-0 z-30 w-64 bg-white dark:bg-gray-800 shadow-lg transform lg:translate-x-0 transition-transform duration-300 ease-in-out"
    :class="{ '-translate-x-full': !sidebarOpen }"
    x-cloak>

    <!-- Logo -->
    <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-white text-lg">code</span>
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
                <span class="material-symbols-outlined w-5 mr-3">dashboard</span>
                <span>Dashboard</span>
            </a>

            <!-- User Management -->
            <div x-data="{ open: {{ request()->routeIs('admin.users.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="flex items-center justify-between w-full px-4 py-3 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="flex items-center">
                        <span class="material-symbols-outlined w-5 mr-3">group</span>
                        <span>User Management</span>
                    </div>
                    <span class="material-symbols-outlined text-xs transition-transform" :class="{ 'rotate-180': open }">expand_more</span>
                </button>

                <div x-show="open" x-collapse class="ml-4 mt-2 space-y-1">
                    <a href="{{ route('admin.users.index') }}"
                        class="flex items-center px-4 py-2 text-sm text-gray-600 dark:text-gray-300 rounded hover:bg-blue-50 dark:hover:bg-gray-700 {{ request()->routeIs('admin.users.index') ? 'text-blue-600 dark:text-blue-400' : '' }}">
                        <span class="material-symbols-outlined w-4 mr-2">list</span>
                        All Users
                    </a>
                    <a href="{{ route('admin.users.create') }}"
                        class="flex items-center px-4 py-2 text-sm text-gray-600 dark:text-gray-300 rounded hover:bg-blue-50 dark:hover:bg-gray-700 {{ request()->routeIs('admin.users.create') ? 'text-blue-600 dark:text-blue-400' : '' }}">
                        <span class="material-symbols-outlined w-4 mr-2">person_add</span>
                        Add User
                    </a>
                </div>
            </div>

            <!-- System Tools -->
            <div x-data="{ open: {{ request()->routeIs('admin.system.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="flex items-center justify-between w-full px-4 py-3 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="flex items-center">
                        <span class="material-symbols-outlined w-5 mr-3">build</span>
                        <span>System Tools</span>
                    </div>
                    <span class="material-symbols-outlined text-xs transition-transform" :class="{ 'rotate-180': open }">expand_more</span>
                </button>

                <div x-show="open" x-collapse class="ml-4 mt-2 space-y-1">
                    <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-600 dark:text-gray-300 rounded hover:bg-blue-50 dark:hover:bg-gray-700">
                        <span class="material-symbols-outlined w-4 mr-2">description</span>
                        Logs
                    </a>
                    <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-600 dark:text-gray-300 rounded hover:bg-blue-50 dark:hover:bg-gray-700">
                        <span class="material-symbols-outlined w-4 mr-2">task_alt</span>
                        Queue Monitor
                    </a>
                    <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-600 dark:text-gray-300 rounded hover:bg-blue-50 dark:hover:bg-gray-700">
                        <span class="material-symbols-outlined w-4 mr-2">bolt</span>
                        Cache Management
                    </a>
                </div>
            </div>

            <!-- System Configuration -->
            <div x-data="{ open: {{ request()->routeIs('devAdmin.systemConfig.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="flex items-center justify-between w-full px-4 py-3 text-gray-700 dark:text-gray-200 rounded-lg 
                    hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="flex items-center">
                        <span class="material-symbols-outlined w-5 mr-3">tune</span>
                        <span>System Config</span>
                    </div>
                    <span class="material-symbols-outlined text-xs transition-transform" :class="{ 'rotate-180': open }">expand_more</span>
                </button>

                <div x-show="open" x-collapse class="ml-4 mt-2 space-y-1">
                    <!-- Admin Panel -->
                    <div x-data="{ openSub: {{ request()->routeIs('devAdmin.systemConfig.adminPanel.*') ? 'true' : 'false' }} }">
                        <button @click="openSub = !openSub"
                            class="flex items-center justify-between w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 rounded hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors">
                            <div class="flex items-center">
                                <span class="material-symbols-outlined w-4 mr-2">admin_panel_settings</span>
                                <span>Admin Panel</span>
                            </div>
                            <span class="material-symbols-outlined text-xs transition-transform" :class="{ 'rotate-180': openSub }">expand_more</span>
                        </button>

                        <div x-show="openSub" x-collapse class="ml-4 mt-2 space-y-1">
                            <a href="{{ route('devAdmin.systemConfig.adminPanel.menu') }}"
                                class="flex items-center px-4 py-2 text-sm text-gray-600 dark:text-gray-300 rounded hover:bg-blue-50 dark:hover:bg-gray-700 {{ request()->routeIs('devAdmin.systemConfig.adminPanel.menu') ? 'text-blue-600 dark:text-blue-400' : '' }}">
                                <span class="material-symbols-outlined w-4 mr-2">menu</span> Menu
                            </a>
                            <a href="{{ route('devAdmin.systemConfig.adminPanel.internalLink') }}"
                                class="flex items-center px-4 py-2 text-sm text-gray-600 dark:text-gray-300 rounded hover:bg-blue-50 dark:hover:bg-gray-700 {{ request()->routeIs('devAdmin.systemConfig.adminPanel.internalLink') ? 'text-blue-600 dark:text-blue-400' : '' }}">
                                <span class="material-symbols-outlined w-4 mr-2">link</span> Internal Link
                            </a>
                            <a href="{{ route('devAdmin.systemConfig.adminPanel.menuSorting') }}"
                                class="flex items-center px-4 py-2 text-sm text-gray-600 dark:text-gray-300 rounded hover:bg-blue-50 dark:hover:bg-gray-700 {{ request()->routeIs('devAdmin.systemConfig.adminPanel.menuSorting') ? 'text-blue-600 dark:text-blue-400' : '' }}">
                                <span class="material-symbols-outlined w-4 mr-2">sort</span> Menu Sorting
                            </a>
                        </div>
                    </div>

                    <!-- Software -->
                    <div x-data="{ openSub: {{ request()->routeIs('devAdmin.systemConfig.software.*') ? 'true' : 'false' }} }">
                        <button @click="openSub = !openSub"
                            class="flex items-center justify-between w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 rounded hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors">
                            <div class="flex items-center">
                                <span class="material-symbols-outlined w-4 mr-2">terminal</span>
                                <span>Software</span>
                            </div>
                            <span class="material-symbols-outlined text-xs transition-transform" :class="{ 'rotate-180': openSub }">expand_more</span>
                        </button>

                        <div x-show="openSub" x-collapse class="ml-4 mt-2 space-y-1">
                            <a href="{{ route('devAdmin.systemConfig.software.menu') }}"
                                class="flex items-center px-4 py-2 text-sm text-gray-600 dark:text-gray-300 rounded hover:bg-blue-50 dark:hover:bg-gray-700 {{ request()->routeIs('devAdmin.systemConfig.software.menu') ? 'text-blue-600 dark:text-blue-400' : '' }}">
                                <span class="material-symbols-outlined w-4 mr-2">menu</span> Menu
                            </a>
                            <a href="{{ route('devAdmin.systemConfig.software.internalLink') }}"
                                class="flex items-center px-4 py-2 text-sm text-gray-600 dark:text-gray-300 rounded hover:bg-blue-50 dark:hover:bg-gray-700 {{ request()->routeIs('devAdmin.systemConfig.software.internalLink') ? 'text-blue-600 dark:text-blue-400' : '' }}">
                                <span class="material-symbols-outlined w-4 mr-2">link</span> Internal Link
                            </a>
                            <a href="{{ route('devAdmin.systemConfig.software.menuSorting') }}"
                                class="flex items-center px-4 py-2 text-sm text-gray-600 dark:text-gray-300 rounded hover:bg-blue-50 dark:hover:bg-gray-700 {{ request()->routeIs('devAdmin.systemConfig.software.menuSorting') ? 'text-blue-600 dark:text-blue-400' : '' }}">
                                <span class="material-symbols-outlined w-4 mr-2">sort</span> Menu Sorting
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Database -->
            <a href="#"
                class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors">
                <span class="material-symbols-outlined w-5 mr-3">database</span>
                <span>Database</span>
            </a>

            <!-- Settings -->
            <a href="#"
                class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors">
                <span class="material-symbols-outlined w-5 mr-3">settings</span>
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
