<div class="bg-white shadow rounded-2xl p-6">
  @if (!empty($errorMessage))
    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3">
      {{ $errorMessage }}
    </div>
  @endif

  <form method="POST" action="{{ route('dev.execute') }}" data-submit-lock>
    @csrf

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
      >{{ old('sql', $sql ?? '') }}</textarea>
    </div>

    @error('sql')
      <div class="mb-4 text-sm text-red-600">{{ $message }}</div>
    @enderror

    <div class="flex gap-3">
      <button
        type="submit"
        data-submit-button
        data-loading-text="Executing..."
        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
      >
        Execute
      </button>

      @if(!empty($executionId))
        <a
          href="{{ route('dev.export.excel', ['execution_id' => $executionId]) }}"
          class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700"
        >
          Export Excel
        </a>
        <a
          href="{{ route('dev.export.json', ['execution_id' => $executionId]) }}"
          class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
        >
          Export JSON
        </a>
      @else
        <button
          type="button"
          disabled
          class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest opacity-50 cursor-not-allowed"
        >
          Export Excel
        </button>
        <button
          type="button"
          disabled
          class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest opacity-50 cursor-not-allowed"
        >
          Export JSON
        </button>
      @endif
    </div>
  </form>

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
</div>
