<template>
    <DevAdminLayout>
        <div class="space-y-6">

            <!-- ── Page Header ─────────────────────────────────────────── -->
            <div class="flex items-center justify-between bg-white dark:bg-gray-900 p-6 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm dark:shadow-xl transition-colors">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                        <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400">database</span>
                        Database Management
                    </h2>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Infrastructure overview, one-click backup utilities &amp; history.</p>
                </div>
                <div class="text-right">
                    <div class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Active Schema</div>
                    <div class="text-sm font-mono font-bold text-emerald-600 dark:text-emerald-400">{{ dbName }}</div>
                </div>
            </div>

            <!-- ── Tabs ────────────────────────────────────────────────── -->
            <div class="flex gap-1 bg-gray-100 dark:bg-gray-900/60 p-1 rounded-xl border border-gray-200 dark:border-gray-800 w-fit transition-colors">
                <button v-for="tab in tabs" :key="tab.key"
                    @click="activeTab = tab.key"
                    :class="[
                        'px-5 py-2 rounded-lg text-sm font-semibold transition-all flex items-center gap-2 cursor-pointer',
                        activeTab === tab.key
                            ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm border border-gray-200 dark:border-gray-700'
                            : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'
                    ]">
                    <span class="material-symbols-outlined text-base">{{ tab.icon }}</span>
                    {{ tab.label }}
                </button>
            </div>

            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- TAB 1 — Infrastructure                                    -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <div v-show="activeTab === 'infrastructure'" class="space-y-4">
                <!-- Stats bar -->
                <div class="bg-white dark:bg-gray-900 rounded-xl p-5 border border-gray-200 dark:border-gray-800 flex items-center gap-6 shadow-sm dark:shadow-xl transition-colors">
                    <div class="flex-1">
                        <div class="flex justify-between mb-1">
                            <span class="text-xs font-bold text-gray-500 uppercase">Total Footprint</span>
                            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">{{ totalSize }} MB</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-2">
                            <div class="bg-emerald-500 h-2 rounded-full transition-all duration-1000 w-full"></div>
                        </div>
                    </div>
                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="text-[10px] text-gray-500 dark:text-gray-400 uppercase font-black">Tables</div>
                        <div class="text-xl font-bold text-gray-900 dark:text-white">{{ tables.length }}</div>
                    </div>
                </div>

                <!-- Tables grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="table in tables" :key="table.name"
                        class="p-4 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 hover:border-emerald-500/40 transition-all group shadow-sm dark:shadow-md">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-lg group-hover:text-emerald-500 transition-colors">table</span>
                                <span class="text-sm font-bold text-gray-800 dark:text-gray-200 truncate max-w-[160px]">{{ table.name }}</span>
                            </div>
                            <span class="text-[10px] font-mono bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700">
                                {{ table.size_mb }}MB
                            </span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-800 h-1 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full"
                                :style="{ width: Math.min((table.size_mb / (totalSize || 1)) * 100 * 5, 100) + '%' }">
                            </div>
                        </div>
                        <div class="flex justify-between mt-2">
                            <span class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-tighter">Weight</span>
                            <span class="text-[10px] font-bold text-gray-600 dark:text-gray-400">{{ ((table.size_mb / (totalSize || 1)) * 100).toFixed(1) }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- TAB 2 — Create Backup                                     -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <div v-show="activeTab === 'backup'" class="space-y-6">

                <!-- Active Connection Info Banner -->
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-5 flex items-center justify-between shadow-sm dark:shadow-xl transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                            <span class="material-symbols-outlined">dns</span>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase font-bold tracking-wider">Active Database Environment (.env)</div>
                            <div class="text-sm font-bold text-gray-900 dark:text-white font-mono flex items-center gap-2 mt-0.5">
                                <span>{{ dbDefaults?.username }}@{{ dbDefaults?.host }}:{{ dbDefaults?.port }}</span>
                                <span class="text-xs font-normal text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 px-2 py-0.5 rounded-full">Database: {{ dbName }}</span>
                            </div>
                        </div>
                    </div>
                    <button @click="showAdvanced = !showAdvanced" class="text-xs text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white flex items-center gap-1 bg-gray-100 dark:bg-gray-800 px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700 transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-sm">{{ showAdvanced ? 'expand_less' : 'tune' }}</span>
                        {{ showAdvanced ? 'Hide Advanced Options' : 'Custom Host / Overrides' }}
                    </button>
                </div>

                <!-- Collapsible Advanced Credentials Form -->
                <transition name="fade">
                    <div v-if="showAdvanced" class="bg-white dark:bg-gray-900 rounded-xl border border-blue-200 dark:border-blue-500/30 shadow-sm dark:shadow-xl overflow-hidden transition-colors">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 bg-gradient-to-r from-blue-500/10 to-transparent flex items-center gap-3">
                            <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">tune</span>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">Custom Connection Overrides</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Override default .env settings to target an external or secondary MySQL database.</p>
                            </div>
                        </div>

                        <div class="p-6 space-y-6">
                            <!-- Host + Port + Database -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Host</label>
                                    <input v-model="credentials.host" type="text" placeholder="127.0.0.1"
                                        class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 transition-colors font-mono" />
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Port</label>
                                    <input v-model="credentials.port" type="number" placeholder="3306"
                                        class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 transition-colors font-mono" />
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Database Name</label>
                                    <input v-model="credentials.database" type="text" placeholder="my_database"
                                        class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 transition-colors font-mono" />
                                </div>
                            </div>

                            <!-- Username + Password -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Username</label>
                                    <input v-model="credentials.username" type="text" placeholder="root"
                                        class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 transition-colors font-mono" />
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Password</label>
                                    <div class="relative">
                                        <input v-model="credentials.password" :type="showPassword ? 'text' : 'password'" placeholder="••••••••"
                                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2.5 pr-10 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 transition-colors font-mono" />
                                        <button type="button" @click="showPassword = !showPassword"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                            <span class="material-symbols-outlined text-base">{{ showPassword ? 'visibility_off' : 'visibility' }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Test Connection Button -->
                            <div class="flex items-center gap-4">
                                <button @click="testConnection" :disabled="testingConnection"
                                    class="flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-700 text-blue-600 dark:text-blue-400 rounded-lg text-sm font-semibold transition-all disabled:opacity-50 cursor-pointer">
                                    <span class="material-symbols-outlined text-base" :class="{ 'animate-spin': testingConnection }">
                                        {{ testingConnection ? 'refresh' : 'wifi_tethering' }}
                                    </span>
                                    {{ testingConnection ? 'Testing...' : 'Test Connection' }}
                                </button>

                                <transition name="fade">
                                    <span v-if="connectionStatus" :class="[
                                        'flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold',
                                        connectionStatus.success
                                            ? 'bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-500/30'
                                            : 'bg-rose-100 dark:bg-rose-500/15 text-rose-700 dark:text-rose-400 border border-rose-300 dark:border-rose-500/30'
                                    ]">
                                        <span class="material-symbols-outlined text-sm">{{ connectionStatus.success ? 'check_circle' : 'cancel' }}</span>
                                        {{ connectionStatus.message }}
                                    </span>
                                </transition>
                            </div>
                        </div>
                    </div>
                </transition>

                <!-- Backup Type Selection -->
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm dark:shadow-xl overflow-hidden transition-colors">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 bg-gradient-to-r from-emerald-500/10 to-transparent flex items-center gap-3">
                        <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400">archive</span>
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">Select Backup Type</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Choose what data to dump from the database.</p>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                            <!-- Type 1: Data Only -->
                            <label :class="[
                                'relative flex flex-col gap-3 p-5 rounded-xl border-2 cursor-pointer transition-all group',
                                backupType === 'data_only'
                                    ? 'border-amber-500 bg-amber-50 dark:bg-amber-500/10'
                                    : 'border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50 hover:border-gray-300 dark:hover:border-gray-700'
                            ]">
                                <input type="radio" v-model="backupType" value="data_only" class="sr-only" />
                                <div :class="['w-10 h-10 rounded-xl flex items-center justify-center transition-all',
                                    backupType === 'data_only' ? 'bg-amber-500/20 border border-amber-500/40' : 'bg-gray-200 dark:bg-gray-700 border border-gray-300 dark:border-gray-600']">
                                    <span class="material-symbols-outlined text-amber-600 dark:text-amber-400 text-xl">table_rows</span>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">Data Only</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">Exports all table rows without structure. Useful for data migration to an existing schema.</div>
                                </div>
                                <div :class="['absolute top-3 right-3 w-4 h-4 rounded-full border-2 transition-all',
                                    backupType === 'data_only' ? 'border-amber-500 bg-amber-500' : 'border-gray-300 dark:border-gray-600']">
                                    <div v-if="backupType === 'data_only'" class="w-1.5 h-1.5 bg-white rounded-full absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"></div>
                                </div>
                                <div class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider w-fit"
                                    :class="backupType === 'data_only' ? 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400'">
                                    --no-create-info
                                </div>
                            </label>

                            <!-- Type 2: Structure Only -->
                            <label :class="[
                                'relative flex flex-col gap-3 p-5 rounded-xl border-2 cursor-pointer transition-all group',
                                backupType === 'structure_only'
                                    ? 'border-purple-500 bg-purple-50 dark:bg-purple-500/10'
                                    : 'border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50 hover:border-gray-300 dark:hover:border-gray-700'
                            ]">
                                <input type="radio" v-model="backupType" value="structure_only" class="sr-only" />
                                <div :class="['w-10 h-10 rounded-xl flex items-center justify-center transition-all',
                                    backupType === 'structure_only' ? 'bg-purple-500/20 border border-purple-500/40' : 'bg-gray-200 dark:bg-gray-700 border border-gray-300 dark:border-gray-600']">
                                    <span class="material-symbols-outlined text-purple-600 dark:text-purple-400 text-xl">schema</span>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">Structure Only</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">Exports CREATE TABLE statements only. Ideal for creating clean schema replicas.</div>
                                </div>
                                <div :class="['absolute top-3 right-3 w-4 h-4 rounded-full border-2 transition-all',
                                    backupType === 'structure_only' ? 'border-purple-500 bg-purple-500' : 'border-gray-300 dark:border-gray-600']">
                                    <div v-if="backupType === 'structure_only'" class="w-1.5 h-1.5 bg-white rounded-full absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"></div>
                                </div>
                                <div class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider w-fit"
                                    :class="backupType === 'structure_only' ? 'bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-400' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400'">
                                    --no-data
                                </div>
                            </label>

                            <!-- Type 3: Complete -->
                            <label :class="[
                                'relative flex flex-col gap-3 p-5 rounded-xl border-2 cursor-pointer transition-all group',
                                backupType === 'complete'
                                    ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-500/10'
                                    : 'border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50 hover:border-gray-300 dark:hover:border-gray-700'
                            ]">
                                <input type="radio" v-model="backupType" value="complete" class="sr-only" />
                                <div :class="['w-10 h-10 rounded-xl flex items-center justify-center transition-all',
                                    backupType === 'complete' ? 'bg-emerald-500/20 border border-emerald-500/40' : 'bg-gray-200 dark:bg-gray-700 border border-gray-300 dark:border-gray-600']">
                                    <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400 text-xl">database</span>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">Complete Backup</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">Full dump including schema &amp; data. Recommended for disaster recovery.</div>
                                </div>
                                <div :class="['absolute top-3 right-3 w-4 h-4 rounded-full border-2 transition-all',
                                    backupType === 'complete' ? 'border-emerald-500 bg-emerald-500' : 'border-gray-300 dark:border-gray-600']">
                                    <div v-if="backupType === 'complete'" class="w-1.5 h-1.5 bg-white rounded-full absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"></div>
                                </div>
                                <div class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider w-fit"
                                    :class="backupType === 'complete' ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400'">
                                    Full Dump
                                </div>
                            </label>

                        </div>

                        <!-- One-Click Create Backup Button -->
                        <div class="mt-6 flex items-center gap-4">
                            <button @click="createBackup" :disabled="backingUp"
                                :class="[
                                    'flex items-center gap-2 px-6 py-3 rounded-lg text-sm font-bold transition-all shadow-lg disabled:opacity-50 cursor-pointer',
                                    backupType === 'data_only'
                                        ? 'bg-amber-500 hover:bg-amber-400 text-gray-900 shadow-amber-500/20'
                                        : backupType === 'structure_only'
                                            ? 'bg-purple-600 hover:bg-purple-500 text-white shadow-purple-500/20'
                                            : 'bg-emerald-600 hover:bg-emerald-500 text-white shadow-emerald-500/20'
                                ]">
                                <span class="material-symbols-outlined text-base" :class="{ 'animate-spin': backingUp }">
                                    {{ backingUp ? 'refresh' : 'play_arrow' }}
                                </span>
                                {{ backingUp ? 'Creating Backup...' : 'Generate Backup Now' }}
                            </button>
                            <span class="text-xs text-gray-500">
                                Saves to <code class="text-gray-700 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded font-mono border border-gray-200 dark:border-gray-700">storage/app/backups/</code>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- TAB 3 — Backup History                                    -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <div v-show="activeTab === 'history'" class="space-y-4">

                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm dark:shadow-xl overflow-hidden transition-colors">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 bg-gradient-to-r from-indigo-500/10 to-transparent flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-indigo-600 dark:text-indigo-400">history</span>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">Backup History</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ backups.length }} backup{{ backups.length !== 1 ? 's' : '' }} found</p>
                            </div>
                        </div>
                    </div>

                    <!-- Empty state -->
                    <div v-if="backups.length === 0" class="flex flex-col items-center justify-center py-20 text-center">
                        <span class="material-symbols-outlined text-gray-300 dark:text-gray-700 text-6xl mb-4">cloud_off</span>
                        <p class="text-gray-500 font-medium">No backups generated yet</p>
                        <p class="text-gray-400 dark:text-gray-600 text-sm mt-1">Create your first backup from the <button @click="activeTab = 'backup'" class="text-emerald-600 dark:text-emerald-400 hover:underline">Create Backup</button> tab.</p>
                    </div>

                    <!-- Table -->
                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50">
                                    <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Filename</th>
                                    <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Database</th>
                                    <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Size</th>
                                    <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Created</th>
                                    <th class="text-right px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                                <tr v-for="backup in backups" :key="backup.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
                                    <!-- Filename -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-sm">description</span>
                                            <span class="font-mono text-xs text-gray-800 dark:text-gray-300 max-w-[240px] truncate" :title="backup.filename">{{ backup.filename }}</span>
                                        </div>
                                    </td>
                                    <!-- Database -->
                                    <td class="px-6 py-4">
                                        <span class="font-mono text-xs text-gray-700 dark:text-gray-400">{{ backup.db_name }}</span>
                                        <div class="text-[10px] text-gray-400 dark:text-gray-600">{{ backup.db_host }}</div>
                                    </td>
                                    <!-- Type badge -->
                                    <td class="px-6 py-4">
                                        <span :class="[
                                            'inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold',
                                            backup.backup_type === 'data_only'
                                                ? 'bg-amber-100 dark:bg-amber-500/15 text-amber-700 dark:text-amber-400 border border-amber-300 dark:border-amber-500/30'
                                                : backup.backup_type === 'structure_only'
                                                    ? 'bg-purple-100 dark:bg-purple-500/15 text-purple-700 dark:text-purple-400 border border-purple-300 dark:border-purple-500/30'
                                                    : 'bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-500/30'
                                        ]">
                                            <span class="material-symbols-outlined text-xs">{{
                                                backup.backup_type === 'data_only' ? 'table_rows'
                                                : backup.backup_type === 'structure_only' ? 'schema'
                                                : 'database'
                                            }}</span>
                                            {{ backup.backup_type_label }}
                                        </span>
                                    </td>
                                    <!-- Size -->
                                    <td class="px-6 py-4">
                                        <span class="text-gray-600 dark:text-gray-400 font-mono text-xs">{{ backup.file_size_formatted }}</span>
                                    </td>
                                    <!-- Status -->
                                    <td class="px-6 py-4">
                                        <button v-if="backup.status === 'failed'" @click="showErrorMsg(backup)"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 dark:bg-rose-500/15 text-rose-700 dark:text-rose-400 border border-rose-300 dark:border-rose-500/30 hover:underline cursor-pointer">
                                            <span class="material-symbols-outlined text-xs">error</span>
                                            Failed (View Error)
                                        </button>
                                        <span v-else
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-500/30">
                                            <span class="material-symbols-outlined text-xs">check_circle</span>
                                            Completed
                                        </span>
                                    </td>
                                    <!-- Date -->
                                    <td class="px-6 py-4">
                                        <span class="text-gray-600 dark:text-gray-400 text-xs">{{ backup.created_at }}</span>
                                    </td>
                                    <!-- Actions -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <a v-if="backup.status === 'completed'"
                                                :href="route('devAdmin.database.backup.download', backup.id)"
                                                class="flex items-center gap-1 px-3 py-1.5 bg-gray-100 dark:bg-gray-800 hover:bg-blue-500/20 border border-gray-300 dark:border-gray-700 hover:border-blue-500/40 text-gray-700 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 rounded-lg text-xs font-semibold transition-all">
                                                <span class="material-symbols-outlined text-sm">download</span>
                                                Download
                                            </a>
                                            <button @click="confirmDelete(backup)"
                                                class="flex items-center gap-1 px-3 py-1.5 bg-gray-100 dark:bg-gray-800 hover:bg-rose-500/20 border border-gray-300 dark:border-gray-700 hover:border-rose-500/40 text-gray-700 dark:text-gray-400 hover:text-rose-600 dark:hover:text-rose-400 rounded-lg text-xs font-semibold transition-all cursor-pointer">
                                                <span class="material-symbols-outlined text-sm">delete</span>
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </DevAdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import DevAdminLayout from '@/Layouts/DevAdminLayout.vue'
import Swal from 'sweetalert2'
import axios from 'axios'

const props = defineProps({
    tables:     Array,
    totalSize:  [Number, String],
    dbName:     String,
    backups:    Array,
    dbDefaults: Object,
})

// ── Tabs ────────────────────────────────────────────────────────────────────
const tabs = [
    { key: 'infrastructure', label: 'Infrastructure', icon: 'storage' },
    { key: 'backup',         label: 'Create Backup',  icon: 'backup'  },
    { key: 'history',        label: 'Backup History', icon: 'history' },
]
const activeTab     = ref('infrastructure')
const showAdvanced  = ref(false)

// ── Credentials (default empty -> backend automatically uses .env) ─────────
const credentials = reactive({
    host:     '',
    port:     '',
    database: '',
    username: '',
    password: '',
})
const showPassword      = ref(false)
const testingConnection = ref(false)
const connectionStatus  = ref(null)

const testConnection = async () => {
    testingConnection.value = true
    connectionStatus.value  = null
    try {
        const { data } = await axios.post(route('devAdmin.database.testConnection'), credentials)
        connectionStatus.value = { success: true, message: data.message, version: data.version }
    } catch (err) {
        const msg = err.response?.data?.message ?? 'Connection failed.'
        connectionStatus.value = { success: false, message: msg }
    } finally {
        testingConnection.value = false
    }
}

// ── Backup ───────────────────────────────────────────────────────────────────
const backupType = ref('complete')
const backingUp  = ref(false)

const isDarkMode = () => document.documentElement.classList.contains('dark')

const createBackup = () => {
    backingUp.value = true
    router.post(route('devAdmin.database.backup'), {
        ...credentials,
        backup_type: backupType.value,
    }, {
        preserveScroll: true,
        onSuccess: (page) => {
            backingUp.value = false
            const msg = page.props.flash?.success
            if (msg) {
                Swal.fire({
                    toast: true, position: 'top-end',
                    showConfirmButton: false, timer: 4000, timerProgressBar: true,
                    icon: 'success', title: msg,
                    background: isDarkMode() ? '#1f2937' : '#ffffff',
                    color: isDarkMode() ? '#f9fafb' : '#111827',
                })
                activeTab.value = 'history'
            }
        },
        onError: () => {
            backingUp.value = false
            Swal.fire({
                toast: true, position: 'top-end',
                showConfirmButton: false, timer: 4000, timerProgressBar: true,
                icon: 'error', title: 'Backup failed. Check server log/credentials and try again.',
                background: isDarkMode() ? '#1f2937' : '#ffffff',
                color: isDarkMode() ? '#f9fafb' : '#111827',
            })
        },
        onFinish: () => { backingUp.value = false },
    })
}

// ── Show Error Details ────────────────────────────────────────────────────────
const showErrorMsg = (backup) => {
    Swal.fire({
        title: 'Backup Failed Reason',
        html: `<pre class="text-left text-xs bg-gray-900 text-rose-300 p-3 rounded overflow-x-auto font-mono max-h-60">${backup.error_message || 'Unknown error occurred.'}</pre>`,
        icon: 'error',
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Close',
        background: isDarkMode() ? '#111827' : '#ffffff',
        color: isDarkMode() ? '#f9fafb' : '#111827',
    })
}

// ── Delete ────────────────────────────────────────────────────────────────────
const confirmDelete = (backup) => {
    Swal.fire({
        title: 'Delete Backup?',
        html: `<p class="text-sm ${isDarkMode() ? 'text-gray-400' : 'text-gray-600'}">This will permanently delete <code class="text-rose-500 font-mono">${backup.filename}</code> from the server.</p>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: isDarkMode() ? '#374151' : '#e5e7eb',
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel',
        background: isDarkMode() ? '#111827' : '#ffffff',
        color: isDarkMode() ? '#f9fafb' : '#111827',
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('devAdmin.database.backup.delete', backup.id), {
                preserveScroll: true,
                onSuccess: (page) => {
                    const msg = page.props.flash?.success
                    if (msg) {
                        Swal.fire({
                            toast: true, position: 'top-end',
                            showConfirmButton: false, timer: 3000,
                            icon: 'success', title: msg,
                            background: isDarkMode() ? '#1f2937' : '#ffffff',
                            color: isDarkMode() ? '#f9fafb' : '#111827',
                        })
                    }
                },
            })
        }
    })
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
