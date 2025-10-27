@extends('layouts.devAdmin')

@section('title', 'Database Info')
@section('page-title', 'Database Overview')
@section('page-description', 'Check current database table sizes and usage.')

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
        {{ $dbName }} — Total Size: {{ number_format($totalSize, 2) }} MB
    </h2>

    <table class="min-w-full text-sm text-left text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
            <tr>
                <th class="px-4 py-2">Table Name</th>
                <th class="px-4 py-2">Size (MB)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tables as $table)
                <tr class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-4 py-2">{{ $table->name }}</td>
                    <td class="px-4 py-2">{{ number_format($table->size_mb, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
