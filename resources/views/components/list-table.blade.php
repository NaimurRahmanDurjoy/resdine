@props([
    'headers' => [],
    'pagination' => null,
    'items' => [],
    'sortable' => [],
    'empty' => 'No data available.',
    'footer' => null,
])

@php
$currentSort = request('sort', 'created_at');
$currentDirection = request('direction', 'desc');
@endphp

<div class="m-4 bg-white dark:bg-gray-900 rounded-md shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm text-gray-700 dark:text-gray-300">
            <thead class="bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 uppercase text-sm font-semibold tracking-wide">
                <tr>
                    @foreach ($headers as $key => $header)
                        @php
                            $isSortable = isset($sortable[$key]);
                            $sortField = $sortable[$key] ?? null;
                            $isActive = $isSortable && $currentSort === $sortField;
                            $newDirection = $isActive && $currentDirection === 'asc' ? 'desc' : 'asc';
                            $sortUrl = $isSortable ? request()->fullUrlWithQuery([
                                'sort' => $sortField,
                                'direction' => $newDirection,
                                'search' => request('search'),
                            ]) : '#';
                        @endphp

                        <th scope="col" class="px-6 py-3 text-left whitespace-nowrap">
                            @if ($isSortable)
                                <a href="{{ $sortUrl }}" class="flex items-center space-x-1 hover:text-indigo-900 dark:hover:text-indigo-400 transition-colors">
                                    <span>{{ $header }}</span>
                                    <span 
                                        class="material-symbols-outlined text-base transition-transform duration-200 ease-in-out 
                                        {{ $isActive ? 'text-indigo-700 dark:text-indigo-300' : 'text-gray-400 dark:text-gray-500' }}
                                        {{ $isActive && $currentDirection === 'asc' ? 'rotate-180' : '' }}">
                                        arrow_drop_down
                                    </span>
                                </a>
                            @else
                                <span>{{ $header }}</span>
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>

            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-100 dark:divide-gray-800">
                @if ($items->count())
                    {{ $rows }}
                @else
                    <tr>
                        <td colspan="{{ count($headers) }}" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            {{ $empty }}
                        </td>
                    </tr>
                @endif
            </tbody>

            @if ($footer)
                <tfoot class="bg-gray-50 dark:bg-gray-800">
                    {{ $footer }}
                </tfoot>
            @endif
        </table>
    </div>

    @if ($pagination)
        <div class="px-5 py-2 bg-indigo-50 dark:bg-indigo-900/30 border-t border-indigo-100 dark:border-indigo-800 rounded-b-lg">
            {{ $pagination->withQueryString()->links() }}
        </div>
    @endif
</div>
