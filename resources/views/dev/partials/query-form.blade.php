<div class="bg-white shadow rounded-2xl p-6">
    @if (!empty($errorMessage))
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3">
            {{ $errorMessage }}
        </div>
    @endif

    <form method="GET" action="{{ route('dev.index') }}">
        <div class="mb-4">
            <label for="sql" class="block text-sm font-medium text-gray-700 mb-2">
                SQL Statement
            </label>
            <textarea
                id="sql"
                name="sql"
                rows="8"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="Only SELECT statements are allowed..."
            >{{ old('sql', $sql ?? request('sql')) }}</textarea>
        </div>

        @error('sql')
            <div class="mb-4 text-sm text-red-600">{{ $message }}</div>
        @enderror

        <div class="flex gap-3">
            <button
                type="submit"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
            >
                Execute
            </button>
            <button
                type="button"
                disabled
                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest opacity-50 cursor-not-allowed"
            >
                Export Excel (TODO)
            </button>
            <button
                type="button"
                disabled
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest opacity-50 cursor-not-allowed"
            >
                Export JSON (TODO)
            </button>
        </div>
    </form>
</div>
