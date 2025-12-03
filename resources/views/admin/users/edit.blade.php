@extends('layouts.admin')

@section('page-title', 'Edit User')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-sm">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit User</h1>
        <p class="text-gray-600">Update the User details</p>
    </div>


        <!-- Actions -->
        <div class="mt-8 flex space-x-3">
            <button type="submit"
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                Update User
            </button>
            <a href=""
               class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                Cancel
            </a>
        </div>
</div>
@endsection
