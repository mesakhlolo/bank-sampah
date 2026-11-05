# AGENTS.md — Bank Sampah

Laravel 12 / PHP 8.4 waste-bank app. Blade + Alpine.js + Tailwind CSS v4; SQLite by default, MySQL/MariaDB supported. Admin/operator use Breeze session auth; the public customer portal uses a custom session, **not** Laravel auth.

## Read first

- `docs/PROJECT.md` — domain model, business rules, portal security, history.
- `docs/PLAN.md` — roadmap and decisions. It is partly stale (for example, it claims CI exists); trust code and config over prose.
- Skills: load `bank-sampah-domain` before touching ledger/models, `security-review-portal` before portal/session changes, and `testing-best-practices` before tests.

## Commands

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed   # resets DB: 2 users, 50 customers, ~300 transactions
composer run dev                   # serve + queue + pail + Vite
npm run build                      # required after Blade/CSS/JS changes

vendor/bin/pint --dirty            # format changed PHP (never `php artisan pint`)
php artisan test --compact         # full suite
php artisan test --compact tests/Feature/DepositManagementTest.php
php artisan test --compact --filter='operator can create withdrawal'
```

Verification order: `pint --dirty` → targeted tests → `test --compact` → `npm run build` when frontend changed. No CI workflow, PHPStan/Psalm, or JS linter is configured; these local gates are the source of truth.

## Architecture

- **Thin controllers.** Authorize via Policy, validate via Form Request, delegate to a Service, return view/redirect. No business logic, reporting SQL, or `DB::transaction` in controllers.
- **Services own transactions.** `DepositService`, `WithdrawalService`, `ReportService`. Any write touching more than one table must be wrapped in `DB::transaction`.
- **Form Requests + Policies.** All writes use a Form Request; `authorize()` uses the Policy. No inline `$request->validate()`.
- **Route model binding.** Prefer it over manual `find()`.
- **Models stay focused.** Relationships, casts, scopes, and derived attributes. Cross-model business rules go in a Service. Do not add a repository layer.
- **Lists eager-load and paginate.** `with()` on every relationship rendered in a list; paginate every list. `LedgerController` currently does not paginate—fix it when touched.
- **Date filters use `App\Support\DateRange`.** `whereDate()` defeats the index; bare `Y-m-d` comparisons drop the final day.

## Code style

- Laravel 12 wiring lives in `bootstrap/app.php`; there is no `Http/Kernel.php`.
- Type-hint parameters and return types; use constructor promotion; always use curly braces.
- New models use `casts()`, not `$casts`.
- Remove dead methods, scopes, routes, and views instead of leaving them behind.

## Domain invariants

- **Balance is computed, never stored.** `SUM(KREDIT) - SUM(DEBIT)` from `ledger_entries`. Never add `customers.balance`; never use `get()->sum()`.
- **Direction is intentional.** Deposit = `KREDIT` (increases); withdrawal = `DEBIT` (decreases). Do not "fix" it.
- **Prices are snapshotted.** `DepositItem` stores `price_per_kg` and `subtotal`; history is immutable when master prices change.
- **Price lookup.** Use `getActivePriceOn($date)`. If missing, throw—never fall back to 0 or the latest price.
- **Prevent duplicate price dates.** There is no unique constraint on `(waste_category_id, effective_from)` yet; add one when touching prices.
- **Codes.** `DEP-YYYYMMDD-####`, `WD-YYYYMMDD-####`, `CUST-####`. Generation is read-then-increment and racy; use the service retry pattern, and `Customer::save()` retry for `CUST-*`. `member_code`/`unique_code` auto-generate in `creating`; do not set them manually except in focused tests.
- **Status.** `deposits.status` is `draft|final`; everything is currently `final`. Report/dashboard queries must filter `status='final'` (some existing queries do not—fix when touched).
- **Withdrawals.** The balance check must be atomic with the insert. The current service locks the code row, not the customer; SQLite ignores `lockForUpdate()`.
- **Relations.** `WasteCategory::prices()` and `wastePrices()` are the same relation; do not duplicate logic.
- **Money.** Store decimals; format Rupiah in the UI.

## Security invariants

- **Registration is currently open and defaults new users to `operator`.** Do not extend this. Admin/operator accounts must be admin-created or invited; close public registration when touching auth.
- **`verified` is not a real gate.** `User` does not implement `MustVerifyEmail`; do not rely on the `verified` middleware.
- **Role checks need two layers.** Enforce role at route group/middleware and in the Policy; do not rely on a single layer.
- **Portal is not Laravel auth.** It uses `session('portal_customer_id')`, `portal_login_time`, `portal_ip`, and middleware `customer.session`. Do not use `Auth::user()` there.
- **Portal session lifecycle.** Call `session()->regenerate()` after successful code verification; `invalidate()` + `regenerateToken()` on logout. Current code only `forget()`s the keys.
- **Portal ownership.** Check that the requested deposit belongs to the session customer before rendering.
- **Never log full `unique_code`.** Use `substr($code, 0, 6).'******'`.
- **No hard delete of history.** Do not cascade-delete `ledger_entries`, `deposits`, `withdrawals`, or `deposit_items` when a customer/category is removed. Use `restrict` or soft deletes. Current FKs cascade—do not add more.
- **Do not leak internals.** Never show `$e->getMessage()` to users; log details and return a safe Indonesian message.
- **Secrets.** Do not commit `.env` or log passwords/tokens.

## UI rules

- **One design system.** Green primary, gray neutral, red danger; one radius scale, one button style, one table style. Do not introduce indigo/blue as a second primary.
- **Indonesian UI, English code.** All user-facing copy is Indonesian; identifiers remain English.
- **No emoji in operational UI.** Admin and portal use Heroicons inline SVG, not emoji. Landing marketing may use illustrations.
- **Reusable Blade components.** Put shared UI in `resources/views/components/` (`x-flash`, `x-table`, `x-button`, `x-badge`, `x-empty-state`, `x-page-header`, `x-stat-card`). Do not copy-paste flash/table/button markup across views.
- **Forms use components.** Prefer Breeze/Blade inputs over raw `<input>` in business forms.
- **DomPDF uses tables.** No flexbox/grid in PDF templates.
- **TomSelect conflict is known.** Global Bootstrap 5 CSS plus `!important` overrides fights Tailwind; keep one theme.
- **No dead script hooks.** Do not add `@push('scripts')` unless the layout renders `@stack('scripts')`.

## Prohibitions

- No repository pattern.
- No Livewire/Inertia or another JS framework; stay Blade + Alpine.
- No API/Sanctum. `routes/api.php` is not registered and uses an uninstalled package; remove it rather than extend it.
- No new dependencies, models, or top-level folders without approval.
- No `.env` changes without explicit confirmation.
- No deleting or weakening tests.
- No dead code, `dd()`/`dump()`, or `env()` outside `config/`.
- No hard delete of transaction history.
- No public registration expansion.
- No emoji in operational UI.
- No reversing `KREDIT`/`DEBIT` or storing balance.
- No silent price fallback.

## Known traps

- `waste-categories.show` route exists but the view is missing → 500.
- Views reference non-existent attributes: `$customer->code` (use `member_code`), `$entry->description`/`transaction_date`, `$deposit->deposit_code` (use `code`), `$item->weight` (use `weight_kg`). Fix, do not copy.
- Price lookups in Blade loops (`waste-categories/index`, `waste-categories/edit`, `deposits/create`) and per-item lookups in `DepositService` cause N+1; batch them when touching those paths.
- `TelescopeServiceProvider` is registered in all environments while Telescope is `require-dev`; `composer install --no-dev` can fail at boot.
- The portal timeout is measured from `portal_login_time` (not sliding); update docs/tests if you change it.
- Eager-load `limit(1)` in `with()` is per-parent in Laravel 12; do not replace it with a global limit.
- `docs/PLAN.md` claims CI exists; `.github/workflows/` does not.

## Tests

- Pest 3. Feature tests auto-use `Tests\TestCase` + `RefreshDatabase` via `tests/Pest.php`; DB is in-memory SQLite, session/cache arrays, queue sync.
- Use factories. `DepositFactory` does not create ledger entries—if a test needs balance, create entries or use `DepositService`.
- Add tests for behavior changes; run targeted tests first, then the full suite.
- Do not create tinker scripts when tests cover the behavior.

## Tooling

- Laravel Boost MCP is configured in `opencode.json`: use `application-info`, `database-schema`, `database-query`, and `search-docs` before version-specific Laravel work.
- Create files with `php artisan make:* --no-interaction`.
