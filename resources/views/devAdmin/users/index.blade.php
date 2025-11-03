@extends('layouts.devAdmin')

@section('page-title', 'Users')

@section('content')
<!-- Modern Container -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-50 to-white px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Users</h1>
                <p class="text-gray-600">Manage your Users</p>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="m-4 flex justify-between items-center">
        <form method="GET" action="{{ route('devAdmin.users.index') }}" class="flex space-x-2">
            <input
                type="text"
                name="search"
                placeholder="Search items..."
                value="{{ old('search', $search) }}"
                class="border px-2 py-1 rounded w-48 text-sm focus:outline-none focus:ring" />

            <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 transition">
                Search
            </button>
        </form>

        <a href="{{ route('devAdmin.users.create') }}" class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-green-700 flex items-center space-x-2">
            <span class="material-symbols-outlined text-sm ">add</span>Add Users
        </a>
    </div>

    <x-list-table
        :headers="['Name', 'Email', 'Phone', 'Branch', 'Role', 'Status', 'Verified', 'Actions']"
        :items="$users"
        :pagination="$users"
        :sortable="[0 => 'name', 1 => 'email', 2 => 'branch_id', ]">
        <x-slot:rows>
            @foreach ($users as $user)
            <tr class="hover:bg-indigo-50 transition">
                <td class="px-6 py-1 font-medium text-gray-900">{{ $user->name }}</td>
                <td class="px-6 py-1 font-medium text-gray-900">{{ $user->email }}</td>
                <td class="px-6 py-1 font-medium text-gray-900">{{ $user->phone }}</td>
                <td class="px-6 py-1 font-medium text-gray-900">{{ $user->branch->name }}</td>
                <td class="px-6 py-1 font-medium text-gray-900">{{ $user->role->name }}</td>
                <td class="px-6 py-1">
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                        {{ $user->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $user->status ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="px-6 py-1">
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                        {{ $user->email_verified_at ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $user->email_verified_at ? 'Yes' : 'No' }}
                    </span>
                </td>
                <td class="px-6 py-1 whitespace-nowrap">
                    <div class="flex items-center space-x-4 text-base text-gray-600">
                        <!-- Edit -->
                        <a href="{{ route('devAdmin.users.edit', $user->id) }}" title="Edit" class="hover:text-indigo-600 transition">
                            <span class="material-symbols-outlined text-indigo-600" style="font-size: 20px;">edit</span>
                        </a>

                        <!-- Delete -->
                        <form action="{{ route('devAdmin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Delete" class="hover:text-red-600 transition bg-transparent border-0 p-0">
                                <span class="material-symbols-outlined text-red-600" style="font-size: 20px;">delete</span>
                            </button>
                        </form>

                        <!-- Access (Roles/Permissions) -->
                        <a href="" title="Manage Access" class="hover:text-indigo-600 transition">
                            <span class="material-symbols-outlined text-indigo-600" style="font-size: 20px;">security</span>
                        </a>
                    </div>
                </td>
            </tr>
            @endforeach
        </x-slot:rows>

        <x-slot:empty>
            <div class="flex flex-col items-center space-y-2">
                <span class="material-symbols-outlined text-gray-400 text-6xl mb-4">users</span>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No menu users found</h3>
                <p class="text-gray-600 mb-4">Get started by creating your first user.</p>
                <a href="{{ route('devAdmin.users.create') }}"
                    class="inline-block mt-2 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">
                    + Add users
                </a>
            </div>
        </x-slot:empty>

        <x-slot:footer>
            <tr>
                <td colspan="8" class="px-6 py-2 text-sm text-gray-500">
                    <div class="flex justify-between items-center w-full">
                        <span>
                            Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} Items
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
@endsection