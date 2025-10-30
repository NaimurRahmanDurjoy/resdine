@extends('layouts.devAdmin')

@section('title', 'Software Menus')
@section('page-title', 'Software Menu List')
@section('page-description', 'Check current database table sizes and usage.')

@section('content')
@php
    $baseRoute = Str::beforeLast(Route::currentRouteName(), '.');
@endphp

<div class="p-6">
    <h1 class="text-2xl font-semibold mb-6 text-gray-800 dark:text-white">Create Menu</h1>
    <form action="{{ route($baseRoute . '.store') }}" method="POST" class="bg-white dark:bg-gray-800 shadow p-6 rounded-lg space-y-6">
        @include('devAdmin.systemConfig.menu-form')
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Save</button>
    </form>
</div>
@endsection