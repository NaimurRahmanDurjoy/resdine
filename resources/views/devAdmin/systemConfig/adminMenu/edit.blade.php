@extends('layouts.devAdmin')

@section('page-title', 'Admin Menu Edit')

@section('content')
@php
    $baseRoute = Str::beforeLast(Route::currentRouteName(), '.');
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-indigo-50 to-white px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-xl font-bold text-gray-800">Update Menu</h1>
                <p class="text-gray-600 text-sm mt-1">Update menu for your software</p>
            </div>
        </div>
    </div>
    <form action="{{ route($baseRoute . '.update', $menu->id) }}" method="POST" class="bg-white dark:bg-gray-800 shadow p-6 rounded-lg space-y-6">
        @csrf
        @method('PUT')
        @include('devAdmin.systemConfig.menu-form')
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Update</button>
    </form>
</div>
@endsection