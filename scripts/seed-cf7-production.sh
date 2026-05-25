#!/bin/sh
set -eu

SCRIPT_DIR="$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)"
WP_PATH="${WP_PATH:-$(pwd)}"
WP_CLI_BIN="${WP_CLI_BIN:-wp}"

LC_CF7_FORM_TITLE="${LC_CF7_FORM_TITLE:-Luxury Copro Contact}"
LC_CF7_FORM_SLUG="${LC_CF7_FORM_SLUG:-luxury-copro-contact}"
LC_CF7_INSTALL_PLUGIN="${LC_CF7_INSTALL_PLUGIN:-1}"
LC_CF7_OVERWRITE_EXISTING="${LC_CF7_OVERWRITE_EXISTING:-1}"

export LC_CF7_FORM_TITLE
export LC_CF7_FORM_SLUG
export LC_CF7_INSTALL_PLUGIN
export LC_CF7_OVERWRITE_EXISTING

wp_cli() {
  if [ "$(id -u)" = "0" ] || [ "${WP_ALLOW_ROOT:-0}" = "1" ]; then
    "${WP_CLI_BIN}" --allow-root --path="${WP_PATH}" "$@"
    return
  fi

  "${WP_CLI_BIN}" --path="${WP_PATH}" "$@"
}

env_bool_enabled() {
  case "$(printf '%s' "${1:-}" | tr '[:upper:]' '[:lower:]')" in
    1|true|yes|on) return 0 ;;
    *) return 1 ;;
  esac
}

echo "Checking WordPress at ${WP_PATH}..."
wp_cli core is-installed >/dev/null

CURRENT_THEME="$(wp_cli option get stylesheet 2>/dev/null || true)"
if [ "${CURRENT_THEME}" != "luxurycopro-theme" ]; then
  echo "Error: active theme is '${CURRENT_THEME:-unknown}', not 'luxurycopro-theme'." >&2
  echo "Aborting so the CF7 shortcode is not stored against the wrong theme." >&2
  exit 1
fi

if env_bool_enabled "${LC_CF7_INSTALL_PLUGIN}"; then
  echo "Ensuring Contact Form 7 is installed and active..."
  if ! wp_cli plugin is-installed contact-form-7 >/dev/null 2>&1; then
    wp_cli plugin install contact-form-7 --activate
  elif ! wp_cli plugin is-active contact-form-7 >/dev/null 2>&1; then
    wp_cli plugin activate contact-form-7
  fi
else
  echo "Skipping Contact Form 7 installation because LC_CF7_INSTALL_PLUGIN=${LC_CF7_INSTALL_PLUGIN}."
fi

echo "Seeding the production Contact Form 7 form..."
wp_cli eval-file "${SCRIPT_DIR}/seed-cf7-production.php"

echo "Production Contact Form 7 seed complete."
