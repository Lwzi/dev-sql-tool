<div class="bg-white shadow rounded-2xl p-6 overflow-x-auto">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Result</h3>

    @if($results->isEmpty())
        <div class="text-sm text-gray-500">
            No data returned.
        </div>
    @else
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    @foreach($columns as $column)
                        <th class="px-4 py-3 text-left font-medium text-gray-700 whitespace-nowrap">
                            {{ $column }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @foreach($results as $row)
                    <tr>
                        @foreach($columns as $column)
                            <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                {{ $row[$column] ?? '' }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($paginator)
            <div class="mt-4">
                {{ $paginator->links() }}
            </div>
        @endif
    @endif
</div>
