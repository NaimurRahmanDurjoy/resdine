<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-50 to-white px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-xl font-bold text-gray-800">Users List</h1>
                <p class="text-gray-600 text-sm mt-1">Manage User list for the software</p>
            </div>
        </div>
    </div>

    <!-- Filters & Actions -->
    <div class="m-4 flex justify-between items-center">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex space-x-2 ajax-search">
            <input
                type="text"
                name="search"
                placeholder="Search users..."
                value="{{ old('search', $search) }}"
                class="border px-2 py-1 rounded w-48 text-sm focus:outline-none focus:ring" />

            <button type="submit" class="px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700 transition">
                Search
            </button>
        </form>

        <!-- Add new user -->
        <button class="add-new px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-green-700 flex items-center space-x-2">
            <span class="material-symbols-outlined text-sm">add</span>
            <span>Add User</span>
        </button>
    </div>

    <!-- Table Container -->
    <div id="table-container">
        <x-list-table
            :headers="['Name', 'Phone', 'Email', 'Status', 'Verified', 'Actions']"
            :items="$users"
            :pagination="$users"
            :sortable="[0 => 'name', 3 => 'status', 4 => 'email_verified_at']">

            <x-slot:rows>
                @foreach ($users as $user)
                <tr class="hover:bg-indigo-50 transition">
                    <td class="px-6 py-1 font-medium text-gray-900">{{ $user->name }}</td>
                    <td class="px-6 py-1 font-medium text-gray-900">{{ $user->phone }}</td>
                    <td class="px-6 py-1 font-medium text-gray-900">{{ $user->email }}</td>

                    <td class="px-6 py-1">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                    {{ $user->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $user->status ? 'Active' : 'Inactive' }}
                        </span>
                    </td>

                    <td class="px-6 py-1">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                    {{ $user->email_verified_at ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $user->email_verified_at ? 'Verified' : 'Unverified' }}
                        </span>
                    </td>

                    <td class="px-6 py-1 whitespace-nowrap">
                        <div class="flex items-center space-x-4 text-base text-gray-600">
                            <!-- Edit -->
                            <button class="edit-btn" data-id="{{ $user->id }}" view="modal" title="Edit">
                                <span class="material-symbols-outlined text-indigo-600" style="font-size: 20px;">edit</span>
                            </button>

                            <!-- Delete -->
                            <button class="delete-btn" data-id="{{ $user->id }}" title="Delete">
                                <span class="material-symbols-outlined text-red-600" style="font-size: 20px;">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </x-slot:rows>

            <x-slot:empty>
                <div class="flex flex-col items-center space-y-2">
                    <span class="material-symbols-outlined text-gray-300 text-6xl">user</span>
                    <h3 class="text-lg font-semibold text-gray-700">No users found</h3>
                    <p class="text-gray-500 text-sm">Start by adding a new user to the software.</p>
                    <button class="add-new inline-block mt-2 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition" view="modal">
                        + Add user
                    </button>
                </div>
            </x-slot:empty>

            <x-slot:footer>
                <tr>
                    <td colspan="6" class="px-6 py-2 text-sm text-gray-500">
                        <div class="flex justify-between items-center w-full">
                            <span>
                                Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users
                            </span>
                            <span>
                                Page {{ $users->currentPage() }} of {{ $users->lastPage() }}
                            </span>
                        </div>
                    </td>
                </tr>
            </x-slot:footer>
        </x-list-table>
    </div>
</div>
