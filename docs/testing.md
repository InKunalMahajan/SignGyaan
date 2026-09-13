# SignGyaan Testing Strategy

Phase 17 makes testing a repeatable release gate for SignGyaan.

## Test layers

1. **Unit tests** — small business rules and pure logic.
2. **Feature tests** — HTTP routes, middleware, authorization, validation and rendered responses.
3. **Integration tests** — assessment, mastery, AI/RAG and database interactions across services.
4. **Regression tests** — previous phase contracts must keep passing after new work.
5. **Build checks** — Vite/Tailwind assets must compile successfully.
6. **Dependency audits** — Composer and npm advisories are checked before release.

## Phase 17 critical regression suite

`tests/Feature/TestingPhase17Test.php` protects these platform contracts:

- `/up`, `/`, and the public guest dashboard remain reachable.
- critical named routes remain registered.
- guests cannot open private learner, teacher, admin, search, or report pages.
- learner, parent, teacher, and admin users can open only their own role dashboard.
- inactive accounts are removed from protected workspaces.

This complements the existing specialized suites for curriculum, lessons, assessments, mastery, dashboards, AI, RAG, accessibility, search, reports, and security.

## Local commands

Use PHP 8.4.1+ because the current dependency lock requires it.

```powershell
php artisan optimize:clear
php artisan test --filter=TestingPhase17Test
php artisan test
npm run build
composer audit
npm audit --audit-level=high
```

A repeatable combined application/build command is also available:

```powershell
composer test:ci
```

## Continuous integration

`.github/workflows/tests.yml` runs on pull requests and pushes to `main`.

The workflow:

- uses PHP 8.4 and Node 20;
- installs locked Composer and npm dependencies;
- prepares a testing environment;
- builds frontend assets;
- runs the full Laravel test suite;
- runs Composer dependency audit;
- runs npm audit at high severity or above.

No production secrets are required by the test workflow. Tests that involve AI providers must use the existing fake/test provider paths and must never make real paid provider calls.

## Release gate

Before merging a feature branch:

- the focused feature test passes;
- the full test suite passes;
- frontend build passes;
- no unexpected database migration is required;
- Composer audit is reviewed;
- npm audit is reviewed;
- accessibility, authorization and ownership regressions are considered for any affected flow.

Do not use `php artisan migrate:fresh` against a real SignGyaan database. Tests use isolated SQLite in-memory storage through `phpunit.xml`.

## Writing new tests

For each new behavior:

- test the allowed path;
- test the forbidden or invalid path;
- test role/ownership boundaries when user data is involved;
- prefer factories and `RefreshDatabase`;
- do not depend on production records;
- fake external providers and avoid network calls;
- keep assertions about stable user-visible or domain behavior instead of fragile implementation details.
