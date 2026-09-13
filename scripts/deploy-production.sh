#!/usr/bin/env bash
set -euo pipefail

: "${APP_ROOT:?APP_ROOT is required, for example /var/www/signgyaan}"
: "${RELEASE_ARCHIVE:?RELEASE_ARCHIVE is required}"
: "${RELEASE_ID:?RELEASE_ID is required}"

KEEP_RELEASES="${KEEP_RELEASES:-5}"
PHP_BIN="${PHP_BIN:-php}"
RELEASES_DIR="$APP_ROOT/releases"
SHARED_DIR="$APP_ROOT/shared"
RELEASE_DIR="$RELEASES_DIR/$RELEASE_ID"
CURRENT_LINK="$APP_ROOT/current"

if [[ ! -f "$RELEASE_ARCHIVE" ]]; then
    echo "Release archive not found: $RELEASE_ARCHIVE" >&2
    exit 1
fi

if [[ ! -f "$SHARED_DIR/.env" ]]; then
    echo "Missing $SHARED_DIR/.env" >&2
    echo "Copy .env.production.example there, fill real production values, and generate APP_KEY before deploying." >&2
    exit 1
fi

mkdir -p \
    "$RELEASES_DIR" \
    "$SHARED_DIR/storage/app/public" \
    "$SHARED_DIR/storage/framework/cache/data" \
    "$SHARED_DIR/storage/framework/sessions" \
    "$SHARED_DIR/storage/framework/views" \
    "$SHARED_DIR/storage/logs"

if [[ -e "$RELEASE_DIR" ]]; then
    echo "Release already exists: $RELEASE_DIR" >&2
    exit 1
fi

mkdir -p "$RELEASE_DIR"
tar -xzf "$RELEASE_ARCHIVE" -C "$RELEASE_DIR"

rm -rf "$RELEASE_DIR/storage"
ln -s "$SHARED_DIR/storage" "$RELEASE_DIR/storage"
ln -s "$SHARED_DIR/.env" "$RELEASE_DIR/.env"

cd "$RELEASE_DIR"

$PHP_BIN artisan storage:link --force
$PHP_BIN artisan migrate --force
$PHP_BIN artisan optimize

ln -sfn "$RELEASE_DIR" "$APP_ROOT/current.next"
mv -Tf "$APP_ROOT/current.next" "$CURRENT_LINK"

cd "$CURRENT_LINK"
$PHP_BIN artisan queue:restart || true

# Keep the newest releases while never deleting the active one.
ACTIVE_RELEASE="$(readlink -f "$CURRENT_LINK")"
mapfile -t OLD_RELEASES < <(find "$RELEASES_DIR" -mindepth 1 -maxdepth 1 -type d -printf '%T@ %p\n' | sort -nr | awk -v keep="$KEEP_RELEASES" 'NR > keep {sub(/^[^ ]+ /, ""); print}')
for old_release in "${OLD_RELEASES[@]:-}"; do
    if [[ -n "$old_release" && "$(readlink -f "$old_release")" != "$ACTIVE_RELEASE" ]]; then
        rm -rf "$old_release"
    fi
done

rm -f "$RELEASE_ARCHIVE"

echo "Deployment complete: $RELEASE_ID"
echo "Current release: $(readlink -f "$CURRENT_LINK")"
