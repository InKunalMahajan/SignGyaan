# SignGyaan

SignGyaan is an accessible Laravel learning management system with role-based workspaces for Learners, Teachers, Parents, and Admins. The learning architecture separates curriculum content, teaching delivery, learner progress, assessment, mastery, reporting, accessibility, AI assistance, and grounded knowledge retrieval.

## Stack

- Laravel 12
- PHP 8.4 recommended for the current lock file
- MySQL in production
- SQLite in the automated test environment
- Tailwind CSS 4 + Vite
- Laravel AI SDK + OpenAI integration

## Local setup

```powershell
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan migrate
npm run build
php artisan serve
```

Do not use `php artisan migrate:fresh` on an existing SignGyaan database.

## Quality gate

Run before merging or deploying:

```powershell
php artisan optimize:clear
php artisan test
npm run build
composer audit
npm audit --audit-level=high
```

GitHub Actions runs the same core regression/build/security checks for pull requests and `main`.

## Production deployment

Phase 18 adds a release-based deployment foundation with:

- manual GitHub Actions production deployment
- verified test/build/security gate before release
- SSH artifact transfer
- atomic `current` symlink releases
- shared `.env` and persistent storage
- MySQL migrations with `--force`
- Laravel optimization
- queue restart
- `/up` health checks
- Nginx example
- systemd queue and scheduler examples
- code rollback without unsafe automatic database rollback

See [`docs/deployment.md`](docs/deployment.md) for the complete server, GitHub Environment, first-deploy, health-check, backup, and rollback procedure.

Production configuration starts from `.env.production.example`. Never commit the real production `.env`, SSH keys, database credentials, API keys, or database backups.

## Important production paths

```text
/var/www/signgyaan/
├── current -> releases/<release-id>
├── incoming/
├── releases/
└── shared/
    ├── .env
    └── storage/
```

Nginx must serve only:

```text
/var/www/signgyaan/current/public
```

## Health endpoint

Laravel exposes:

```text
GET /up
```

The deployment workflow checks this endpoint after activating every production release.

## Documentation

- `docs/accessibility.md` — accessibility and Deaf-first authoring guidance
- `docs/search.md` — role-aware application search
- `docs/deployment.md` — production deployment and rollback

## Security

Report security issues privately to the repository owner. Do not publish credentials or exploit details in public issues.
