@extends('layouts.devAdmin')

@section('title', 'System Logs')
@section('page-title', 'System Logs')
@section('page-description', 'Review the latest Laravel log entries.')

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Logs</h2>
    <pre class="text-sm bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto h-[70vh]">{{ $logs }}</pre>
</div>
@endsection
