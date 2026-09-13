#!/usr/bin/env bash
set -euo pipefail

: "${APP_ROOT:?APP_ROOT is required, for example /var/www/signgyaan}"
: "${RELEASE_ID:?RELEASE_ID is required}"

PHP_BIN="${PHP_BIN:-php}"
TARGET_RELEASE="$APP_ROOT/releases/$RELEASE_ID"
CURRENT_LINK="$APP_ROOT/current"

if [[ ! -d "$TARGET_RELEASE" ]]; then
    echo "Release not found: $TARGET_RELEASE" >&2
    exit 1
fi

if [[ ! -f "$TARGET_RELEASE/artisan" ]]; then
    echo "Target is not a valid SignGyaan release: $TARGET_RELEASE" >&2
    exit 1
fi

ln -sfn "$TARGET_RELEASE" "$APP_ROOT/current.next"
mv -Tf "$APP_ROOT/current.next" "$CURRENT_LINK"

cd "$CURRENT_LINK"
$PHP_BIN artisan optimize
$PHP_BIN artisan queue:restart || true

echo "Application rollback complete: $RELEASE_ID"
echo "Database migrations were NOT rolled back. Database rollback must be reviewed separately before any down migration."
