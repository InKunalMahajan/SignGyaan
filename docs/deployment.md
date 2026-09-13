# Phase 18 — Production Deployment

SignGyaan uses a release-based deployment layout designed for Laravel 12, PHP 8.4, MySQL, Nginx, HTTPS, database-backed sessions/cache/queues, and GitHub Actions.

## 1. Production architecture

Recommended single-server starting architecture:

- Ubuntu LTS server
- Nginx
- PHP 8.4 FPM and CLI
- MySQL 8+
- HTTPS certificate (Let's Encrypt or equivalent)
- GitHub Actions for verified manual deployments
- systemd queue worker
- systemd Laravel scheduler timer
- `/up` as the HTTP health endpoint

The public web root must be `/var/www/signgyaan/current/public`. Never point Nginx at the repository root.

## 2. Required PHP extensions

Install PHP 8.4 with at least:

`mbstring`, `pdo_mysql`, `curl`, `openssl`, `fileinfo`, `zip`, `xml`, `ctype`, `tokenizer`, and `bcmath`.

Also install Nginx, MySQL, Git, unzip, curl, and a TLS certificate client.

## 3. Server directories

Create the application root and shared folders:

```bash
sudo mkdir -p /var/www/signgyaan/{releases,incoming,shared/storage}
sudo chown -R "$USER":www-data /var/www/signgyaan
sudo chmod -R g+rwX /var/www/signgyaan
```

The deployment script creates the detailed shared storage tree automatically.

## 4. Production environment

Copy `.env.production.example` to the server only:

```bash
cp .env.production.example /var/www/signgyaan/shared/.env
```

Fill real values on the server. Never commit the production `.env` or secrets.

Generate one permanent production application key and keep it stable across deployments:

```bash
cd /path/to/a/signgyaan/release
php artisan key:generate --show
```

Put the generated value in `/var/www/signgyaan/shared/.env` as `APP_KEY=`. Do not generate a new key on each deployment because encrypted sessions/data depend on it.

Production essentials:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.example
SESSION_SECURE_COOKIE=true
DB_CONNECTION=mysql
QUEUE_CONNECTION=database
CACHE_STORE=database
SESSION_DRIVER=database
```

Set `OPENAI_API_KEY` only when the teacher AI/RAG features will be used.

## 5. Database

Create a dedicated MySQL database and least-privilege application user. Use a strong password and keep it only in the server `.env`/secret manager.

Deployments run:

```bash
php artisan migrate --force
```

Never use `php artisan migrate:fresh` in production. Phase 18 rollback intentionally does not run down migrations because application rollback and destructive database rollback are separate decisions.

Before schema-changing deployments, take a verified database backup. A normal MySQL example is:

```bash
mysqldump --single-transaction --routines --triggers signgyaan > signgyaan-$(date +%F-%H%M).sql
```

Store backups outside the web root and test restoration periodically.

## 6. Nginx + HTTPS

Use `ops/nginx/signgyaan.conf.example` as the starting server block. Replace domain names and certificate paths, then:

```bash
sudo nginx -t
sudo systemctl reload nginx
```

The example redirects HTTP to HTTPS and serves only `current/public`.

## 7. Queue worker

Copy the service example:

```bash
sudo cp ops/systemd/signgyaan-queue.service.example /etc/systemd/system/signgyaan-queue.service
sudo systemctl daemon-reload
sudo systemctl enable --now signgyaan-queue
```

Check it with:

```bash
sudo systemctl status signgyaan-queue
```

Each deployment calls `php artisan queue:restart`, so workers gracefully load the new release.

## 8. Scheduler

Copy both scheduler examples:

```bash
sudo cp ops/systemd/signgyaan-scheduler.service.example /etc/systemd/system/signgyaan-scheduler.service
sudo cp ops/systemd/signgyaan-scheduler.timer.example /etc/systemd/system/signgyaan-scheduler.timer
sudo systemctl daemon-reload
sudo systemctl enable --now signgyaan-scheduler.timer
```

Verify:

```bash
systemctl list-timers | grep signgyaan
```

## 9. GitHub production environment

Create a GitHub Environment named `production` and configure deployment protection/approval if desired.

Add these Environment secrets:

- `DEPLOY_HOST` — server hostname/IP
- `DEPLOY_USER` — SSH deployment user
- `DEPLOY_SSH_KEY` — private Ed25519 key used only for deployment
- `DEPLOY_PATH` — normally `/var/www/signgyaan`

Add this Environment variable:

- `DEPLOY_URL` — for example `https://learn.example.com`

The corresponding public key must be in the deployment user's `~/.ssh/authorized_keys` on the server.

## 10. First deployment

The first deployment requires `/var/www/signgyaan/shared/.env` to exist before GitHub Actions runs.

From GitHub, open **Actions → Deploy Production → Run workflow** and type `DEPLOY` in the confirmation field.

The workflow:

1. Checks out `main`.
2. Installs PHP and frontend dependencies.
3. Runs the complete test suite.
4. Builds production assets.
5. Runs Composer and npm security audits.
6. Creates a production artifact with `--no-dev` Composer dependencies.
7. Transfers it over SSH.
8. Creates a new immutable release directory.
9. Links the shared `.env` and `storage`.
10. Runs `storage:link`, migrations, and Laravel optimization.
11. Atomically changes the `current` symlink.
12. Restarts queue workers.
13. Calls `DEPLOY_URL/up` and fails if the application is unhealthy.

## 11. Manual deployment command

If GitHub Actions is unavailable, upload a release archive and run:

```bash
APP_ROOT=/var/www/signgyaan \
RELEASE_ARCHIVE=/var/www/signgyaan/incoming/release.tar.gz \
RELEASE_ID=manual-$(date +%Y%m%d%H%M%S) \
bash scripts/deploy-production.sh
```

## 12. Rollback

List retained releases:

```bash
ls -1t /var/www/signgyaan/releases
```

Then repoint the application to a known-good release:

```bash
APP_ROOT=/var/www/signgyaan \
RELEASE_ID=<release-directory-name> \
bash /var/www/signgyaan/current/scripts/rollback-production.sh
```

After rollback verify:

```bash
curl -fsS https://your-domain.example/up
sudo systemctl status signgyaan-queue
```

A code rollback does not reverse database migrations. Review schema compatibility before selecting an old release.

## 13. Post-deployment checks

Verify all of the following after every production deployment:

- `/up` returns HTTP 200.
- Login works over HTTPS.
- Learner, Teacher, Parent, and Admin authorization remains isolated.
- Uploaded files load through `/storage`.
- Queue worker is active.
- Scheduler timer is active.
- `storage/logs/laravel.log` has no new critical errors.
- `APP_DEBUG=false`.
- secure cookies are enabled.
- the current release points to the expected directory.

Useful commands:

```bash
readlink -f /var/www/signgyaan/current
cd /var/www/signgyaan/current
php artisan about
php artisan migrate:status
php artisan queue:monitor default:100
```

## 14. Release retention and recovery

`deploy-production.sh` retains the newest five releases by default. Override with `KEEP_RELEASES` if needed. The active release is never removed by the cleanup loop.

Keep independent database and user-upload backups. Release retention is not a database or media backup strategy.

## 15. Security rules

- Never commit `.env`, private keys, database dumps, or API keys.
- Use a dedicated non-root SSH deployment user.
- Disable password SSH login where operationally possible.
- Keep GitHub Environment secrets scoped to production.
- Use HTTPS only in production.
- Apply operating system/PHP/MySQL security updates regularly.
- Run `composer audit` and `npm audit --audit-level=high` before deployment.
- Do not deploy when Phase 17 CI is failing.
