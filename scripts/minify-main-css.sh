#!/bin/sh
set -eu

ROOT_DIR="$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)"

if command -v php >/dev/null 2>&1; then
  exec php "${ROOT_DIR}/scripts/minify-main-css.php"
fi

if command -v docker >/dev/null 2>&1; then
  exec docker compose run --rm --no-deps --entrypoint php seed /scripts/minify-main-css.php
fi

echo "PHP or Docker is required to minify the theme CSS." >&2
exit 1
