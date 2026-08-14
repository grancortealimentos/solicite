---
paths:
  - 'app/Repositories/**'
---

# Repositories

## Tests run on SQLite — avoid Postgres-only `ilike`
Production uses `pgsql`, but phpunit.xml sets `DB_CONNECTION=sqlite` / `:memory:` for tests. Postgres' `ilike` operator does not exist in SQLite and breaks feature tests with a syntax error.

For case-insensitive search that works on both drivers, use:
`$query->whereRaw('LOWER(column) LIKE ?', ['%'.mb_strtolower($term).'%'])`
instead of `->where('column', 'ilike', "%{$term}%")`.
