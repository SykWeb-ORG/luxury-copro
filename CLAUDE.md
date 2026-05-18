# Luxury Copro - WordPress Theme

## Project Overview
Luxury real estate & copropriété management website for a client in Marrakech. Built as a custom WordPress theme running in Docker.

## Tech Stack
- **WordPress** custom theme (`luxurycopro-theme`)
- **Docker Compose**: WordPress + MySQL 8.0 + phpMyAdmin
- **Frontend**: Vanilla JS (IIFE), CSS custom properties, GSAP 3.12.5 + ScrollTrigger
- **No build tools** — plain PHP/CSS/JS, no bundler

## Local Development
```bash
docker-compose up -d
# Site: http://localhost:8080
# phpMyAdmin: http://localhost:8081
# DB creds: wp / wp / wordpress
```

## Theme Structure
```
wp-content/themes/luxurycopro-theme/
├── front-page.php      # Main landing page (all sections)
├── header.php          # Nav, loader, cursor, meta tags
├── footer.php          # Footer with legal links
├── functions.php       # CPTs (property, reference), Customizer, SEO
├── 404.php             # Custom 404 page
├── page.php            # Generic page template (legal pages)
├── assets/
│   ├── css/main.css    # All styles, dark/light theme variables
│   ├── js/main.js      # All JS in single IIFE
│   ├── img/refs/       # Reference placeholder images (ref-01.jpg to ref-14.jpg)
│   ├── video/          # Hero background video
│   └── site.webmanifest
```

## Custom Post Types
- **`property`** — Real estate listings (currently hidden via `lc_biens_visible`)
- **`reference`** — Résidences managed by the client (14 active)

## Key Patterns

### GSAP Scroll Animations
- Use `once: true` for all entrance animations — **NEVER use `scrub` for opacity**
- Only use `scrub` for non-conflicting transforms (hero glow parallax, CTA scale)
- Never have multiple ScrollTriggers on the same element for the same property

### Theme Options
- All editable via WordPress Customizer (`lc_get_option()`)
- Sections can be toggled: `lc_biens_visible`, `lc_refs_visible`, `lc_about_visible`

### Adding References
WordPress Admin → Références → Ajouter une référence
- Title, featured image, service type, location, order, active checkbox
- Placeholder images in `assets/img/refs/ref-XX.jpg` used when no featured image set

## Git
- Remote: `github.com:SykWeb-ORG/luxury-copro.git`
- Branch: `master`
