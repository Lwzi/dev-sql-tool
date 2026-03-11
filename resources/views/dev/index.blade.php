<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('SQL Query Tool') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900" x-data="{ sql: '' }">
                    <!-- SQL Input -->
                    <div class="mb-4">
                        <label for="sql" class="block text-sm font-medium text-gray-700 mb-2">
                            SQL Statement
                        </label>
                        <textarea
                            id="sql"
                            x-model="sql"
                            rows="8"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Enter your SELECT statement here..."
                        ></textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3 mb-6">
                        <button
                            type="button"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            style="background-color:#4f46e5;color:#ffffff;"
                        >
                            Execute
                        </button>
                        <button
                            type="button"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                            style="background-color:#16a34a;color:#ffffff;"
                        >
                            Export Excel
                        </button>
                        <button
                            type="button"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            style="background-color:#2563eb;color:#ffffff;"
                        >
                            Export JSON
                        </button>
                    </div>

                    <!-- Info Message Area -->
                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-md text-blue-800 text-sm">
                        <p class="font-medium">ℹ️ Information</p>
                        <p class="mt-1">Only SELECT statements are allowed. Enter your query above and click Execute to see results.</p>
                    </div>

                    <!-- Error Area Placeholder -->
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-md text-red-800 text-sm hidden">
                        <p class="font-medium">❌ Error</p>
                        <p class="mt-1">Error messages will appear here.</p>
                    </div>

                    <!-- Results Area Placeholder -->
                    <div class="border border-gray-200 rounded-md p-6 bg-gray-50">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Query Results</h3>
                        <div class="text-center text-gray-500 py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="mt-2">No results yet. Execute a query to see results here.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
