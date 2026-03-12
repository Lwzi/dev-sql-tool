<section class="dev-sql-panel dev-sql-table-shell">
  <div class="dev-sql-panel-body">
    <div class="dev-sql-panel-header">
      <div>
        <h2 class="dev-sql-section-title">Result Grid</h2>
        <p class="dev-sql-section-copy">
          Review the current page in a wide, readable table with sticky headings and restrained emphasis.
        </p>
      </div>

      @if($paginator)
        <div class="dev-sql-results-meta">
          <span>{{ $paginator->total() }} total rows</span>
          <span>{{ count($columns) }} columns</span>
          <span>Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}</span>
        </div>
      @endif
    </div>

    @if($results->isEmpty())
      <div class="dev-sql-alert dev-sql-alert--warning">
        <span class="dev-sql-alert-title">No Data Returned</span>
        <p class="dev-sql-alert-copy">The query completed, but this execution did not return rows for the current page.</p>
      </div>
    @else
      <div class="dev-sql-table-wrap">
        <table class="dev-sql-table">
          <thead>
            <tr>
              @foreach($columns as $column)
                <th>{{ $column }}</th>
              @endforeach
            </tr>
          </thead>
          <tbody>
            @foreach($results as $row)
              <tr>
                @foreach($columns as $column)
                  <td class="dev-sql-cell" title="{{ $row[$column] ?? '' }}">
                    {{ $row[$column] ?? '' }}
                  </td>
                @endforeach
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      @if($paginator && $paginator->hasPages())
        <div class="dev-sql-pager">
          <div class="dev-sql-pager-meta">
            Showing {{ $results->count() }} rows on this page out of {{ $paginator->total() }} total rows.
          </div>

          <div class="dev-sql-pager-links">
            @if($paginator->onFirstPage())
              <span class="dev-sql-pager-disabled">Previous</span>
            @else
              <a href="{{ $paginator->previousPageUrl() }}" class="dev-sql-pager-link">Previous</a>
            @endif

            @if($paginator->hasMorePages())
              <a href="{{ $paginator->nextPageUrl() }}" class="dev-sql-pager-link">Next</a>
            @else
              <span class="dev-sql-pager-disabled">Next</span>
            @endif
          </div>
        </div>
      @endif
    @endif
  </div>
</section>
