<x-app-layout>
  <div class="dev-sql-page">
    <div class="dev-sql-shell">
      <section class="dev-sql-hero">
        <div class="dev-sql-kicker">SQL Query Tool</div>
        <h1 class="dev-sql-title">Run read-only queries and export clean results.</h1>
        <p class="dev-sql-subtitle">
          Execute a `SELECT`, review the snapshot, then export the full result when needed.
        </p>
        <div class="dev-sql-meta">
          <span class="dev-sql-badge">Only SELECT statements are allowed</span>
          <span class="dev-sql-badge">Exports use the current execution snapshot</span>
        </div>
      </section>

      <div class="dev-sql-stack">
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
  </div>
</x-app-layout>
