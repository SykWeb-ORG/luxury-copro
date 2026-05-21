#!/bin/sh
set -eu

WP_CONTENT_DIR="${WP_CONTENT_DIR:-/var/www/html/wp-content}"
WP_CONFIG_FILE="${WP_CONFIG_FILE:-/var/www/html/wp-config.php}"

fix_permissions() {
  path="$1"

  if [ ! -e "${path}" ]; then
    return
  fi

  chown -R www-data:www-data "${path}"
  find "${path}" -type d -exec chmod 775 {} +
  find "${path}" -type f -exec chmod 664 {} +
}

ensure_script_debug_config() {
  if [ ! -f "${WP_CONFIG_FILE}" ]; then
    return
  fi

  if grep -q "SCRIPT_DEBUG" "${WP_CONFIG_FILE}"; then
    return
  fi

  script_debug_line="define('SCRIPT_DEBUG', filter_var(getenv('SCRIPT_DEBUG') ?: 'false', FILTER_VALIDATE_BOOLEAN));"

  if grep -q "That's all, stop editing" "${WP_CONFIG_FILE}"; then
    sed -i "/That's all, stop editing/i ${script_debug_line}" "${WP_CONFIG_FILE}"
    return
  fi

  printf '\n%s\n' "${script_debug_line}" >> "${WP_CONFIG_FILE}"
}

if [ "$(id -u)" = "0" ]; then
  mkdir -p \
    "${WP_CONTENT_DIR}/plugins" \
    "${WP_CONTENT_DIR}/themes" \
    "${WP_CONTENT_DIR}/languages" \
    "${WP_CONTENT_DIR}/uploads" \
    "${WP_CONTENT_DIR}/upgrade" \
    "${WP_CONTENT_DIR}/upgrade-temp-backup"

  chown www-data:www-data "${WP_CONTENT_DIR}"
  chmod 775 "${WP_CONTENT_DIR}"

  fix_permissions "${WP_CONTENT_DIR}/plugins"
  fix_permissions "${WP_CONTENT_DIR}/themes"
  fix_permissions "${WP_CONTENT_DIR}/languages"
  fix_permissions "${WP_CONTENT_DIR}/uploads"
  fix_permissions "${WP_CONTENT_DIR}/upgrade"
  fix_permissions "${WP_CONTENT_DIR}/upgrade-temp-backup"
fi

ensure_script_debug_config

exec docker-entrypoint.sh "$@"
