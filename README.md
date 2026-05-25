# Luxury Copro

Custom WordPress project for the `luxurycopro-theme`, running locally with Docker.

## Quick Start

1. If your host user is not `1000:1000`, copy `.env.example` to `.env` and set `WORDPRESS_UID` / `WORDPRESS_GID` from `id -u` / `id -g`.
2. Run `docker compose up -d --build`.
3. Follow `docker compose logs -f seed` and wait for `Luxury Copro bootstrap complete.` on the first boot.
4. Open `http://localhost:8080`.
5. Open `http://localhost:8080/wp-admin` for the back office.

Local admin credentials on a fresh clone:

- Username: `devadmin`
- Password: `devpassword`

## Database Seeder

Fresh environments are bootstrapped automatically:

- `scripts/seed-entrypoint.sh` waits for WordPress/MySQL, installs WordPress if needed, activates the theme, then runs the project seed once.
- The seed container installs and activates the free Polylang and Contact Form 7 plugins if they are missing.
- `scripts/seed-database.php` creates the legal pages, theme settings, Contact Form 7 contact form, reference entries, and sample property data used for development.
- Uploaded media is kept in the `wp_uploads` Docker volume so WordPress can write files without host permission issues.

Useful commands:

- Reset to a fresh seeded database: `docker compose down -v && docker compose up -d --build`
- Re-run the seed manually on an existing setup: `docker compose run --rm seed`
- Generate the production stylesheet: `scripts/minify-main-css.sh`

Set `SCRIPT_DEBUG=true` in `.env` to load the unminified theme assets during local development. Leave it `false` to load `assets/css/main.min.css` and `assets/js/main.min.js`.

## Multilingual Setup

Polylang is installed automatically on local bootstrap. After the first boot, open `http://localhost:8080/wp-admin`, run the Polylang setup wizard from the admin notice, and add French as the default language plus any additional languages needed.

Once at least two languages exist, the theme displays a compact language switcher in the desktop and mobile navigation. Main Customizer text fields are also registered under Polylang's string translations so they can be translated per language.

## Production Polylang Seed

Use the production Polylang seed over SSH when the WordPress site already exists, for example on Hostinger shared hosting. It installs/activates Polylang, creates French and English, applies the required Polylang options, seeds Luxury Copro string translations, assigns existing content without a language to French, and flushes rewrites.

It does not install WordPress, activate the theme, change `home` / `siteurl`, delete content, or create demo properties/pages.

From the production WordPress root:

```sh
wp db export before-polylang-$(date +%F-%H%M).sql
WP_PATH="$PWD" scripts/seed-polylang-production.sh
```

Useful overrides:

```sh
LC_POLYLANG_ASSIGN_EXISTING_CONTENT=0 WP_PATH="$PWD" scripts/seed-polylang-production.sh
LC_POLYLANG_HIDE_DEFAULT=0 WP_PATH="$PWD" scripts/seed-polylang-production.sh
```

## Production Contact Form 7 Seed

Use the production CF7 seed over SSH after the WordPress site and `luxurycopro-theme` are already installed. It installs/activates Contact Form 7 if needed, creates or updates only the managed `Luxury Copro Contact` form, stores the form shortcode in the theme option `lc_cf7_shortcode`, and leaves all other content untouched.

It does not install WordPress, activate the theme, change `home` / `siteurl`, delete content, or create demo properties/pages.

From the production WordPress root:

```sh
wp db export before-cf7-$(date +%F-%H%M).sql
LC_CF7_RECIPIENT="contact@example.com" WP_PATH="$PWD" scripts/seed-cf7-production.sh
```

Useful overrides:

```sh
LC_CF7_OVERWRITE_EXISTING=0 WP_PATH="$PWD" scripts/seed-cf7-production.sh
LC_CF7_INSTALL_PLUGIN=0 WP_PATH="$PWD" scripts/seed-cf7-production.sh
LC_CF7_FORM_TITLE="Luxury Copro Contact" LC_CF7_FORM_SLUG="luxury-copro-contact" WP_PATH="$PWD" scripts/seed-cf7-production.sh
```

## Local File Permissions

The local WordPress images remap the container `www-data` user to the host UID/GID configured by `WORDPRESS_UID` and `WORDPRESS_GID`. This keeps the host user and wp-admin running with matching ownership on the bind-mounted `./wp-content` directory, so plugin updates, language updates, and theme writes can use WordPress' direct filesystem method.

On startup, the WordPress container also normalizes ownership for writable `wp-content` paths such as `plugins`, `themes`, `languages`, `uploads`, `upgrade`, and `upgrade-temp-backup`. If you still see old mixed ownership after changing UID/GID values, rebuild the images with `docker compose build --no-cache wordpress seed`.

## Git Workflow

Every feature must be developed in its own branch, reviewed, then merged back into the default branch.

- Current default branch: `master`
- If the repository is renamed to `main` later, keep the same workflow and naming rules

Recommended branch naming:

- `feature/<scope>-<short-description>`
- `fix/<scope>-<short-description>`
- `chore/<scope>-<short-description>`

Examples:

- `feature/db-seeder`
- `feature/properties-modal`
- `fix/footer-legal-links`
- `chore/docker-local-setup`

Expected flow:

1. Branch from `master`.
2. Implement one feature or fix per branch.
3. Open a PR for review.
4. Merge only after review is approved.
