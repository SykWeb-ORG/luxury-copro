#!/bin/sh
set -eu

WP_CONTENT_DIR="${WP_CONTENT_DIR:-/var/www/html/wp-content}"

fix_permissions() {
  path="$1"

  if [ ! -e "${path}" ]; then
    return
  fi

  chown -R www-data:www-data "${path}"
  find "${path}" -type d -exec chmod 775 {} +
  find "${path}" -type f -exec chmod 664 {} +
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

exec docker-entrypoint.sh "$@"
