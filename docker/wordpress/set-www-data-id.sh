#!/bin/sh
set -eu

: "${WORDPRESS_UID:=1000}"
: "${WORDPRESS_GID:=1000}"

if [ "${WORDPRESS_UID}" = "0" ] || [ "${WORDPRESS_GID}" = "0" ]; then
  echo "WORDPRESS_UID and WORDPRESS_GID must not be 0." >&2
  exit 1
fi

current_gid="$(getent group www-data | cut -d: -f3)"
if [ "${current_gid}" != "${WORDPRESS_GID}" ]; then
  if command -v groupmod >/dev/null 2>&1; then
    groupmod -o -g "${WORDPRESS_GID}" www-data
  else
    sed -i "s|^\(www-data:[^:]*:\)[0-9]*|\1${WORDPRESS_GID}|" /etc/group
  fi
fi

current_uid="$(id -u www-data)"
if [ "${current_uid}" != "${WORDPRESS_UID}" ]; then
  if command -v usermod >/dev/null 2>&1; then
    usermod -o -u "${WORDPRESS_UID}" -g "${WORDPRESS_GID}" www-data
  else
    sed -i "s|^\(www-data:[^:]*:\)[0-9]*:[0-9]*|\1${WORDPRESS_UID}:${WORDPRESS_GID}|" /etc/passwd
  fi
fi

mkdir -p /var/www/html
chown www-data:www-data /var/www/html
