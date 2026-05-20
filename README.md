# Luxury Copro

Custom WordPress project for the `luxurycopro-theme`, running locally with Docker.

## Quick Start

1. Run `docker compose up -d`.
2. Follow `docker compose logs -f seed` and wait for `Luxury Copro bootstrap complete.` on the first boot.
3. Open `http://localhost:8080`.
4. Open `http://localhost:8080/wp-admin` for the back office.

Local admin credentials on a fresh clone:

- Username: `devadmin`
- Password: `devpassword`

## Database Seeder

Fresh environments are bootstrapped automatically:

- `scripts/seed-entrypoint.sh` waits for WordPress/MySQL, installs WordPress if needed, activates the theme, then runs the project seed once.
- `scripts/seed-database.php` creates the legal pages, theme settings, reference entries, and sample property data used for development.
- Uploaded media is kept in the `wp_uploads` Docker volume so WordPress can write files without host permission issues.

Useful commands:

- Reset to a fresh seeded database: `docker compose down -v && docker compose up -d`
- Re-run the seed manually on an existing setup: `docker compose run --rm seed`

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
