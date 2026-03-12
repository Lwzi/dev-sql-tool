<section class="dev-sql-panel">
  <div class="dev-sql-panel-body">
    <div class="dev-sql-panel-header">
      <div>
        <h2 class="dev-sql-section-title">Compose Query</h2>
        <p class="dev-sql-section-copy">
          Work in a single large editor, keep the query readable, and treat every execution as an auditable snapshot.
        </p>
      </div>

      @if(!empty($executionId))
        <span class="dev-sql-chip">Execution #{{ $executionId }}</span>
      @endif
    </div>

    <div class="dev-sql-alert dev-sql-alert--warning">
      <span class="dev-sql-alert-title">Safety Boundary</span>
      <p class="dev-sql-alert-copy">
        This tool is intentionally narrow. Only read-only <code>SELECT</code> queries are accepted, and exports reuse the same safety rules.
      </p>
    </div>

    @if (!empty($errorMessage))
      <div class="mt-4 dev-sql-alert dev-sql-alert--error">
        <span class="dev-sql-alert-title">Query Error</span>
        <p class="dev-sql-alert-copy">{{ $errorMessage }}</p>
      </div>
    @endif

    <form method="POST" action="{{ route('dev.execute') }}" data-submit-lock class="mt-6">
      @csrf

      <div>
        <label for="sql" class="dev-sql-label">SQL Statement</label>
        <textarea
          id="sql"
          name="sql"
          rows="8"
          class="dev-sql-textarea"
          placeholder="select id, name, email from users order by id desc"
        >{{ old('sql', $sql ?? '') }}</textarea>
      </div>

      @error('sql')
        <p class="dev-sql-error-copy">{{ $message }}</p>
      @enderror

      <div class="dev-sql-toolbar">
        <button
          type="submit"
          data-submit-button
          data-loading-text="Executing..."
          class="dev-sql-btn dev-sql-btn--primary"
        >
          Execute
        </button>

        @if(!empty($executionId))
          <a
            href="{{ route('dev.export.excel', ['execution_id' => $executionId]) }}"
            class="dev-sql-btn dev-sql-btn--secondary"
          >
            Export Excel
          </a>
          <a
            href="{{ route('dev.export.json', ['execution_id' => $executionId]) }}"
            class="dev-sql-btn dev-sql-btn--secondary"
          >
            Export JSON
          </a>
        @else
          <button
            type="button"
            disabled
            class="dev-sql-btn dev-sql-btn--secondary dev-sql-btn--ghost"
          >
            Export Excel
          </button>
          <button
            type="button"
            disabled
            class="dev-sql-btn dev-sql-btn--secondary dev-sql-btn--ghost"
          >
            Export JSON
          </button>
        @endif

        <p class="dev-sql-toolbar-copy">
          Exports are generated from the full query result, not from the visible page only.
        </p>
      </div>
    </form>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('form[data-submit-lock]').forEach(function (form) {
        if (form.dataset.submitLockBound === '1') {
          return;
        }

        form.dataset.submitLockBound = '1';

        form.addEventListener('submit', function () {
          const button = form.querySelector('[data-submit-button]');

          if (!button || button.disabled) {
            return;
          }

          button.textContent = button.dataset.loadingText || 'Submitting...';
          button.disabled = true;
          button.classList.add('opacity-50', 'cursor-not-allowed');
        });
      });
    });
  </script>
</section>
