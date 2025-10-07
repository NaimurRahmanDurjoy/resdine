@props([
    'headers' => [],
    'pagination' => null,
    'items' => [],
])

<div class="m-4 bg-white rounded-md shadow-lg border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm text-gray-700">
            <!-- Table Head -->
            <thead class="bg-indigo-100 text-indigo-700 uppercase text-sm font-semibold tracking-wide">
                <tr>
                    @foreach ($headers as $header)
                        <th class="px-6 py-3 text-left">
                            {{ $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>

            <!-- Table Body -->
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

            <!-- Optional Footer -->
            @if (isset($footer))
                <tfoot class="bg-gray-50">
                    {{ $footer }}
                </tfoot>
            @endif
        </table>
    </div>

    <!-- Pagination -->
    @if ($pagination)
        <div class="px-5 py-2 bg-indigo-50 border-t border-indigo-100 rounded-b-lg">
            {{ $pagination->withQueryString()->links() }}
        </div>
    @endif

</div>
