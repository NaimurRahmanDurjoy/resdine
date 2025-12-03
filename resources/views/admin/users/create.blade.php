@section('page-title', 'Add User')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-sm" x-data="{ selectedType: '{{ old('type', 1) }}' }">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Add User</h1>
        <p class="text-gray-600">Create a new User for your restaurant</p>
    </div>

    <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-300">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-300">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-300">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-300">
                    <option value="1" {{ isset($user) && $user->status ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ isset($user) && !$user->status ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>


        <!-- Form Actions -->
        <div class="mt-8 flex space-x-3">
            <button type="submit"
                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition flex items-center">
                <span class="material-symbols-outlined mr-2 text-sm">save</span> Save User
            </button>
            <a href="{{ route('admin.users.index') }}"
                class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
</script>
@endsection