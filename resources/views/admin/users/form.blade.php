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
