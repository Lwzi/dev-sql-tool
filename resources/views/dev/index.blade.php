<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      SQL Query Tool
    </h2>
  </x-slot>

  <div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
      @include('dev.partials.query-form', [
          'sql' => $sql,
          'executionId' => $executionId,
          'errorMessage' => $errorMessage,
      ])

      @if(!empty($executionId) || !empty($errorMessage))
        @include('dev.partials.results-panel', [
            'results' => $results,
            'columns' => $columns,
            'paginator' => $paginator,
        ])
      @else
        @include('dev.partials.empty-state')
      @endif
    </div>
  </div>
</x-app-layout>
