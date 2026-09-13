# SignGyaan Security Standard — Phase 16

Phase 16 hardens the existing Laravel application without changing academic, assessment, mastery, AI, search, or report ownership rules.

## 16A — Authentication protection

- Login failures are limited to 5 attempts per minute for the same normalized email + IP combination.
- Registration is limited to 3 attempts per minute per IP address.
- Successful login clears the login limiter and regenerates the session ID.
- Logout invalidates the session and regenerates the CSRF token.
- Inactive accounts remain blocked by the existing active-user middleware.
- Authentication errors stay generic so they do not reveal whether a specific account exists.

## 16B — Password policy

New registrations and learner, teacher, and parent password changes require:

- at least 10 characters;
- at least one letter;
- at least one number;
- password confirmation;
- the current password for an authenticated password change.

Password changes regenerate the current session ID after the stored password is updated.

## 16C — Session and cookie protection

Security defaults are:

- encrypted session payloads;
- HttpOnly session cookies;
- SameSite=Strict by default;
- Secure session cookies automatically default to enabled when `APP_ENV=production` unless explicitly overridden.

Local HTTP development can keep `SESSION_SECURE_COOKIE=false`. Production HTTPS deployments should set it to `true` explicitly.

## 16D — Browser security headers

All web responses receive:

- `X-Content-Type-Options: nosniff`;
- `X-Frame-Options: DENY`;
- `Referrer-Policy: strict-origin-when-cross-origin`;
- `Permissions-Policy: camera=(), microphone=(), geolocation=()`;
- `Cross-Origin-Opener-Policy: same-origin`.

Production HTTPS requests also receive HSTS. Authenticated pages and login/register responses use no-store caching controls so private workspace content is not intentionally cached by shared intermediaries.

A restrictive Content Security Policy is not forced in Phase 16 because SignGyaan currently uses Vite and may contain framework/runtime behavior that should first be nonce-audited. A future CSP should be introduced only after testing every protected workspace, accessibility feature, and AI/search/report screen.

## 16E — Authorization and privacy boundaries

Existing role and ownership middleware remains the authority for access control:

- learners access only their enrolled learning data;
- teachers access only their owned/assigned teaching data;
- parents access only approved linked learners;
- admins use explicitly admin-only routes;
- search and reports preserve the same scopes;
- AI/RAG remains teacher-scoped and must not expose secrets or unnecessary learner personal data.

Security must be enforced on the server. Hiding a button or navigation link is never treated as authorization.

## 16F — File and input safety

Existing validation remains required for all writes. Learner avatars continue to accept only validated image uploads (`jpg`, `jpeg`, `png`, `webp`) with a 2 MB maximum. New upload features must use an allow-list of file types, size limits, generated storage names, and authorization checks.

Do not render untrusted user text as raw Blade/HTML unless it has been intentionally sanitized. Prefer normal `{{ ... }}` Blade escaping.

## Production checklist

Before deployment:

1. Set `APP_ENV=production`.
2. Set `APP_DEBUG=false`.
3. Set `APP_URL` to the final HTTPS URL.
4. Set `SESSION_SECURE_COOKIE=true`.
5. Keep `SESSION_ENCRYPT=true`, `SESSION_HTTP_ONLY=true`, and `SESSION_SAME_SITE=strict` unless a documented integration requires otherwise.
6. Use a unique strong `APP_KEY`; never commit `.env` or API keys.
7. Store the OpenAI key only in the deployment secret/environment system.
8. Use HTTPS at the reverse proxy/web server and redirect plain HTTP to HTTPS.
9. Give the database account only the permissions the application requires.
10. Keep PHP, Composer dependencies, Node dependencies, Laravel, and the host OS patched.
11. Run `composer audit` and `npm audit` as part of release review; assess findings before deployment rather than applying breaking upgrades blindly.
12. Back up the production database and verify restore procedures.
13. Review application logs for repeated authentication failures and unexpected authorization errors without logging passwords, API keys, session IDs, or sensitive learner content.
14. Run the Phase 16 regression test plus existing role/search/report tests before release.

## Verification

Run:

```powershell
php artisan optimize:clear
php artisan test --filter=SecurityPhase16Test
php artisan test --filter=SearchPhase14Test
php artisan test --filter=ReportsPhase15Test
php artisan test --filter=AccessibilityPhase13Test
```

For dependency review:

```powershell
composer audit
npm audit
```

Do not use `php artisan migrate:fresh` on an existing SignGyaan database. Phase 16 adds no database migration.
