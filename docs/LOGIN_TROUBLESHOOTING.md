# SignGyaan local login troubleshooting (Windows + XAMPP)

The login form submits to `AuthController@login`. An account must exist in the **currently configured database**, have the correct password, and have `is_active = 1`. Only Laravel's normal password hashing should be used. **Do not disable password checks or add a default admin password.**

## 1. Confirm the connection

Inside `C:\Users\lenovo\Documents\GitHub\SignGyaan`, inspect your private `.env` locally:

```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=signgyaan
DB_USERNAME=root
DB_PASSWORD=
```

Use your real local credentials; the blank root password is valid only for an XAMPP instance configured that way. Start MySQL in XAMPP. Never commit your `.env`.

```powershell
php artisan config:clear
php artisan migrate:status
php artisan db:show
```

If `db:show` reports **sqlite**, Laravel has not switched to MySQL. Check `.env`, cached config, and any system-level `DB_CONNECTION` override before proceeding. Migrating to MySQL does **not** copy SQLite users.

## 2. Find or create a legitimate account

```powershell
php artisan tinker
```

Within Tinker, use a real email address (do not share it in chat):

```php
App\Models\User::where('email', 'your-email@example.com')->first(['id','email','role','is_active']);
```

- `null` means that address is not present in the currently selected database. Register a new account at `/register`, or restore users from a backup if they should already exist.
- `is_active = false` means the account is disabled. Re-activation should be performed by an authorized administrator, not bypassed in login code.
- If a user exists and is active but password verification fails, use a legitimate account recovery process or an admin-authorized local reset; do not modify the password field with plaintext.
- Public registration deliberately supports learner, parents and teacher roles only; admin accounts must be provisioned by authorized admins.

## 3. Check the application

```powershell
composer install
npm.cmd install
npm.cmd run build
php artisan optimize:clear
php artisan test --filter=LoginTroubleshootingTest
php artisan serve
```

The tests use an isolated testing database; they do not create a production or local administrator.

Open `http://127.0.0.1:8000/login`.

If login continues to fail, capture the **validation message** and `storage/logs/laravel.log` excerpt without sharing passwords, tokens, cookies, or personal data. Also confirm `php artisan db:show` shows the intended database.

## Notes

The code change only normalizes login email input. It **does not** automatically create users, migrate data, reset passwords or modify role permissions.
