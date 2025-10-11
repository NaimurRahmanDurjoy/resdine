@props([
    'headers' => [],
    'pagination' => null,
    'items' => [],
    'sortable' => [],
])

@php
    $currentSort = request('sort', 'created_at');
    $currentDirection = request('direction', 'desc');
@endphp

<div class="m-4 bg-white rounded-md shadow-lg border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm text-gray-700">
            <thead class="bg-indigo-100 text-indigo-700 uppercase text-sm font-semibold tracking-wide">
                <tr>
                    @foreach ($headers as $key => $header)
                        @php
                            $isSortable = isset($sortable[$key]);
                            $sortField = $sortable[$key] ?? null;
                            $isActive = $isSortable && $currentSort === $sortField;
                            $newDirection = $isActive && $currentDirection === 'asc' ? 'desc' : 'asc';
                            $sortUrl = $isSortable ? request()->fullUrlWithQuery([
                                'sort' => $sortField, 
                                'direction' => $newDirection
                            ]) : '#';
                        @endphp

                        <th class="px-6 py-3 text-left">
                            @if($isSortable)
                                <a href="{{ $sortUrl }}" class="flex items-center space-x-1 hover:text-indigo-900 transition">
                                    <span>{{ $header }}</span>
                                    <span class="material-icons text-sm {{ $isActive ? '' : 'text-gray-400' }}">
                                        {{ $isActive ? ($currentDirection === 'asc' ? 'arrow_drop_up' : 'arrow_drop_down') : 'unfold_more' }}
                                    </span>
                                </a>
                            @else
                                {{ $header }}
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-100">
                @if ($items->count())
                    {{ $rows }}
                @else
                    <tr>
                        <td colspan="{{ count($headers) }}" class="px-6 py-12 text-center text-gray-500">
                            {{ $empty ?? 'No data available.' }}
                        </td>
                    </tr>
                @endif
            </tbody>

            @if (isset($footer))
                <tfoot class="bg-gray-50">
                    {{ $footer }}
                </tfoot>
            @endif
        </table>
    </div>

    @if ($pagination)
        <div class="px-5 py-2 bg-indigo-50 border-t border-indigo-100 rounded-b-lg">
            {{ $pagination->withQueryString()->links() }}
        </div>
    @endif
</div>