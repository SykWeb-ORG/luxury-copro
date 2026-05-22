#!/bin/sh
set -eu

SCRIPT_DIR="$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)"
WP_PATH="${WP_PATH:-$(pwd)}"
WP_CLI_BIN="${WP_CLI_BIN:-wp}"

LC_POLYLANG_DEFAULT_LANG="${LC_POLYLANG_DEFAULT_LANG:-fr}"
LC_POLYLANG_LANGUAGES="${LC_POLYLANG_LANGUAGES:-fr,en}"
LC_POLYLANG_HIDE_DEFAULT="${LC_POLYLANG_HIDE_DEFAULT:-1}"
LC_POLYLANG_ASSIGN_EXISTING_CONTENT="${LC_POLYLANG_ASSIGN_EXISTING_CONTENT:-1}"

export LC_POLYLANG_DEFAULT_LANG
export LC_POLYLANG_LANGUAGES
export LC_POLYLANG_HIDE_DEFAULT
export LC_POLYLANG_ASSIGN_EXISTING_CONTENT

wp_cli() {
  if [ "$(id -u)" = "0" ] || [ "${WP_ALLOW_ROOT:-0}" = "1" ]; then
    "${WP_CLI_BIN}" --allow-root --path="${WP_PATH}" "$@"
    return
  fi

  "${WP_CLI_BIN}" --path="${WP_PATH}" "$@"
}

echo "Checking WordPress at ${WP_PATH}..."
wp_cli core is-installed >/dev/null

CURRENT_THEME="$(wp_cli option get stylesheet 2>/dev/null || true)"
if [ "${CURRENT_THEME}" != "luxurycopro-theme" ]; then
  echo "Warning: active theme is '${CURRENT_THEME:-unknown}', not 'luxurycopro-theme'."
  echo "Polylang will still be configured, but Luxury Copro theme strings are only registered when the theme is active."
fi

echo "Ensuring Polylang is installed and active..."
if ! wp_cli plugin is-installed polylang >/dev/null 2>&1; then
  wp_cli plugin install polylang --activate
elif ! wp_cli plugin is-active polylang >/dev/null 2>&1; then
  wp_cli plugin activate polylang
fi

echo "Configuring Polylang languages and string translations..."
wp_cli eval-file "${SCRIPT_DIR}/seed-polylang-production.php"

wp_cli rewrite flush >/dev/null 2>&1 || true

echo "Production Polylang seed complete."
