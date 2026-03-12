<x-public-layout>
    <div class="landing-shell">
      <div class="landing-wrap">
        <div class="landing-nav">
          <div class="landing-brand">Dev SQL Tool</div>

          <div class="landing-nav-links">
            @auth
              <a href="{{ route('dashboard') }}" class="dev-sql-btn dev-sql-btn--secondary">Dashboard</a>
            @else
              <a href="{{ route('login') }}" class="dev-sql-btn dev-sql-btn--primary">Login</a>
            @endauth
          </div>
        </div>

        <section class="landing-hero">
          <div class="landing-kicker">Read-only Workspace</div>
          <h1 class="landing-title">Read-only SQL review and export.</h1>
          <p class="landing-copy">
            Login, execute a <code>SELECT</code>, inspect the snapshot, then export the full result.
          </p>

          <div class="landing-actions">
            @auth
              <a href="{{ route('dev.index') }}" class="dev-sql-btn dev-sql-btn--primary">Open SQL Tool</a>
              <a href="{{ route('dashboard') }}" class="dev-sql-btn dev-sql-btn--secondary">Go to Dashboard</a>
            @else
              <a href="{{ route('login') }}" class="dev-sql-btn dev-sql-btn--primary">Login</a>
            @endauth
          </div>
        </section>

        <div class="landing-grid">
          <article class="landing-card">
            <h2 class="landing-card-title">Read Only</h2>
            <p class="landing-card-copy">Only <code>SELECT</code> statements are accepted throughout execution and export.</p>
          </article>
          <article class="landing-card">
            <h2 class="landing-card-title">Snapshot Based</h2>
            <p class="landing-card-copy">Pagination and exports are tied to execution records instead of leaking raw SQL into URLs.</p>
          </article>
          <article class="landing-card">
            <h2 class="landing-card-title">Export Ready</h2>
            <p class="landing-card-copy">Download full JSON or Excel output from the current execution when review is complete.</p>
          </article>
        </div>
      </div>
    </div>
</x-public-layout>
