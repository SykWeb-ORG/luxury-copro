#!/bin/sh
set -eu

WP_PATH="${WP_PATH:-/var/www/html}"
SEED_SITE_URL="${SEED_SITE_URL:-http://localhost:8080}"
SEED_SITE_TITLE="${SEED_SITE_TITLE:-Luxury Copro}"
SEED_ADMIN_USER="${SEED_ADMIN_USER:-devadmin}"
SEED_ADMIN_PASSWORD="${SEED_ADMIN_PASSWORD:-devpassword}"
SEED_ADMIN_EMAIL="${SEED_ADMIN_EMAIL:-dev@luxury-copro.local}"
SEED_VERSION="${SEED_VERSION:-2026-05-20.1}"
WORDPRESS_DB_HOST="${WORDPRESS_DB_HOST:-db}"
WORDPRESS_DB_USER="${WORDPRESS_DB_USER:-wp}"
WORDPRESS_DB_PASSWORD="${WORDPRESS_DB_PASSWORD:-wp}"
WP_CLI="wp --allow-root --path=${WP_PATH}"

echo "Waiting for WordPress bootstrap files..."
until [ -f "${WP_PATH}/wp-config.php" ]; do
  sleep 2
done

echo "Waiting for MySQL to accept connections..."
until mysqladmin --host="${WORDPRESS_DB_HOST}" --user="${WORDPRESS_DB_USER}" --password="${WORDPRESS_DB_PASSWORD}" --ssl=0 ping >/dev/null 2>&1; do
  sleep 2
done

if ! ${WP_CLI} core is-installed >/dev/null 2>&1; then
  echo "Installing WordPress..."
  ${WP_CLI} core install \
    --url="${SEED_SITE_URL}" \
    --title="${SEED_SITE_TITLE}" \
    --admin_user="${SEED_ADMIN_USER}" \
    --admin_password="${SEED_ADMIN_PASSWORD}" \
    --admin_email="${SEED_ADMIN_EMAIL}" \
    --skip-email
fi

echo "Activating theme and permalink structure..."
${WP_CLI} theme activate luxurycopro-theme >/dev/null
${WP_CLI} rewrite structure "/%postname%/" --hard >/dev/null 2>&1 || true

CURRENT_VERSION="$(${WP_CLI} option get luxury_copro_seed_version 2>/dev/null || true)"

if [ "${CURRENT_VERSION}" != "${SEED_VERSION}" ]; then
  echo "Running project seed ${SEED_VERSION}..."
  ${WP_CLI} eval-file /scripts/seed-database.php
else
  echo "Seed ${SEED_VERSION} already applied."
fi

${WP_CLI} rewrite flush --hard >/dev/null 2>&1 || true

echo "Luxury Copro bootstrap complete."
