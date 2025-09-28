@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold text-center mb-6">Login to Resdine</h2>

@if(session('error'))
    <div class="bg-red-100 text-red-600 p-3 rounded mb-4">
        {{ session('error') }}
    </div>
@endif

<form method="POST" action="{{ route('login') }}" class="space-y-4">
    @csrf

    <div>
        <label class="block text-gray-700">Email</label>
        <input type="email" name="email" required 
               class="w-full p-2 border rounded focus:ring focus:ring-blue-300">
    </div>

    <div>
        <label class="block text-gray-700">Password</label>
        <input type="password" name="password" required 
               class="w-full p-2 border rounded focus:ring focus:ring-blue-300">
    </div>

    <button type="submit" 
            class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
        Login
    </button>
</form>
@endsection
