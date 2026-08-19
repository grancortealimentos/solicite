---
paths:
  - 'tests/Feature/**'
---

# Feature

## DB::shouldReceive('connection')->with('protheus') breaks RefreshDatabase/seeders
Mocking the 'protheus' connection via `DB::shouldReceive('connection')->with('protheus')->andReturn($fake)` swaps the ENTIRE `DB` facade root with a strict Mockery mock — any other DB-facade call in the same test (Artisan `$this->seed(...)`, app code using `DB::transaction(...)`) then fails with "no expectations were specified", even though Eloquent model queries on the default connection keep working fine (Eloquent holds its own connection resolver captured at boot, not via the facade).

Fixes used in tests/Feature/SolicitacaoCreateTest.php:
- Call `$this->seed(...)` BEFORE installing the protheus mock (seeding touches the DB facade internally).
- If application code wraps a write in `DB::transaction(...)` (e.g. SolicitacaoRepository::criar), also stub `DB::shouldReceive('transaction')->andReturnUsing(fn (Closure $c) => $c());` so it still runs.

This only matters when a test combines `RefreshDatabase`/seeding with a Protheus DB mock (see SolicitacaoItensTest.php for a Protheus-only mock with no default-connection use, which needs none of this).
