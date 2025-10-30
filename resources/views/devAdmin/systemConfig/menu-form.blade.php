@csrf

<div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-6 space-y-6">

    <!-- Menu Name & Route -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">
                Menu Name <span class="text-red-500">*</span>
            </label>
            <input type="text" name="name" required
                value="{{ old('name', $menu->name ?? '') }}"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                          bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">
                Route
            </label>
            <input type="text" name="route"
                value="{{ old('route', $menu->route ?? '') }}"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                          bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
        </div>
    </div>

    <!-- Icon & Parent -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">
                Icon (Material Symbol)
            </label>
            <input type="text" name="icon"
                placeholder="e.g. dashboard, settings"
                value="{{ old('icon', $menu->icon ?? '') }}"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                          bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">
                Parent Menu
            </label>
            <select name="parent_id"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                           bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                <option value="">None</option>
                @foreach($parents as $parent)
                <option value="{{ $parent->id }}"
                    {{ old('parent_id', $menu->parent_id ?? '') == $parent->id ? 'selected' : '' }}>
                    {{ $parent->name }}
                </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="flex items-center mt-2 sm:mt-7">
        <input type="checkbox" id="is_active" name="is_active" value="1"
            {{ old('is_active', $menu->is_active ?? true) ? 'checked' : '' }}
            class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
        <label for="is_active" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
            Active
        </label>
    </div>



</div>