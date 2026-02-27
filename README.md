<div align="center">

# ✦ Rares Portfolio

**A dark, minimal, Awwwards-level portfolio — built with pure PHP, no frameworks, no build steps.**

[![PHP](https://img.shields.io/badge/PHP-8.x-7c3aed?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.x-2563eb?style=flat-square&logo=mysql&logoColor=white)](https://mysql.com)
[![Vanilla JS](https://img.shields.io/badge/JS-Vanilla-f59e0b?style=flat-square&logo=javascript&logoColor=black)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![License](https://img.shields.io/badge/License-MIT-22c55e?style=flat-square)](LICENSE)
[![Hosted free on Anybusiness.ro](https://img.shields.io/badge/Hosted_free_on-Anybusiness.ro-5c8cff?style=flat-square)](https://anybusiness.ro)

*Hosted for free on [Anybusiness.ro](https://anybusiness.ro) — free PHP hosting with MySQL & no monthly fees.*

</div>

---

## ✦ What is this?

A production-grade, self-hosted **developer portfolio** built from scratch — no Laravel, no Symfony, no Node.js. Just clean PHP 8, a custom MVC framework, and hand-crafted CSS.

Designed to feel like an [Awwwards](https://www.awwwards.com) website:
- **Dark ambient background** with a subtle grid and corner light leaks
- **Custom cursor** with a lagged ring, spotlight glow (`mix-blend-mode: screen`), and click ripple
- **Smooth scroll reveals** via IntersectionObserver
- **Case study pages** per project with hero cover, meta row, tag pills, prose body, and prev/next navigation
- **Tag filter** on the project grid with live filtering and URL state
- **Fully responsive** with a hamburger nav on mobile
- **SEO-ready**: per-page `<title>`, `<meta description>`, OpenGraph, Twitter Card, and `<link rel="canonical">`

---

## ✦ Features

| Area | Details |
|------|---------|
| **MVC Architecture** | Custom router with reflection-based param injection, singleton PDO, no global state |
| **Admin Panel** | Secure CRUD for projects, categories, tags, and a settings row |
| **Security** | CSRF (session-bound, `hash_equals`), file-based rate limiter, role-checked admin guard, HSTS, CSP, secure cookie flags |
| **Image Uploads** | GD-based re-encode to WebP — upload JPG/PNG/WebP, always stored optimally |
| **SEO** | `SeoService` merges per-page overrides with settings-row defaults, injects OG + Twitter Card into layout |
| **CSS System** | 5-file cascade: tokens → base → layout → components → admin |
| **Cursor System** | Dot + lagged ring (rAF lerp at t=0.12) + radial `screen`-blend glow + double-ring click ripple |
| **Performance** | `font-display: swap`, `loading="lazy"`, `aspect-ratio`, GPU-only transitions, no layout shifts |

---

## ✦ Tech Stack

```
Backend   PHP 8.x  ·  PDO / MySQL  ·  vlucas/phpdotenv
Frontend  Vanilla JS (no framework)  ·  CSS custom properties
Font      Space Grotesk Variable (self-hosted WOFF2)
Hosting   Apache / shared PHP hosting — no Composer, no npm at runtime
```

---

## ✦ Getting Started

### 1 — Clone & install

```bash
git clone https://github.com/banarares/portfolio.git
cd portfolio
composer install
```

### 2 — Configure environment

```bash
cp .env.example .env
```

Edit `.env` and fill in your database credentials:

```env
APP_ENV=local
APP_DEBUG=true

DB_HOST=localhost
DB_NAME=your_database_name
DB_USER=your_database_user
DB_PASSWORD=your_secret_password

PORTFOLIO_USER_ID=1
```

### 3 — Create the database schema

```bash
mysql -u YOUR_USER -p YOUR_DATABASE < database/schema.sql
```

### 4 — Seed demo data

```bash
php database/seed.php
```

This creates:
- **Admin user** — `admin@example.com` / `admin123` *(change immediately after login)*
- **Settings row** — placeholder site name, tagline, URLs
- **Sample category** — Web Development
- **Sample tags** — PHP, MySQL, Vanilla JS
- **Sample project** — a fully published featured case study

### 5 — Point your web server to `/public`

For Apache, the included `.htaccess` handles routing. For Nginx:

```nginx
root /var/www/html/public;

location / {
    try_files $uri $uri/ /index.php$is_args$args;
}
```

### 6 — Log in and customise

Visit `/admin/login` — use the credentials from step 4, then:

1. Go to **[/admin/settings](http://localhost/admin/settings)** → update your name, tagline, canonical URL, email, and social links
2. Go to **[/admin/projects](http://localhost/admin/projects)** → replace the sample project with your real work
3. Upload a cover image for each project (JPG / PNG / WebP, max 5 MB — auto-converted to WebP)

---

## ✦ Project Structure

```
├── app/
│   ├── Controllers/      # HomeController, ProjectsController, Admin*
│   ├── Core/             # App, Router, DB, View, Auth, Csrf, Cache, RateLimiter
│   ├── Models/           # Project, Category, Tag, User, Setting
│   ├── Services/         # SeoService, ImageService, PortfolioContext, SlugService
│   └── Views/            # PHP templates (layouts/main.php as shell)
├── database/
│   ├── schema.sql        # CREATE TABLE IF NOT EXISTS — run once
│   └── seed.php          # Demo data — run once via CLI
├── public/
│   ├── index.php         # Front controller — security headers, routing bootstrap
│   ├── assets/
│   │   ├── css/
│   │   │   ├── 00-tokens.css     # Design system variables
│   │   │   ├── 10-base.css       # Global base, typography, scrollbar
│   │   │   ├── 20-layout.css     # Sections, hero, spacing
│   │   │   ├── 30-components.css # Buttons, cards, cursor, mobile nav
│   │   │   ├── 40-admin.css      # Admin panel only
│   │   │   └── app.css           # Imports all CSS files in order
│   │   ├── js/
│   │   │   ├── app.js                  # Reveal, cursor, ripple, nav burger, tag filter
│   │   │   └── projects-loadmore.js    # Infinite scroll / load-more for project grid
│   │   └── fonts/                # SpaceGrotesk-Variable.woff2 (self-hosted)
│   └── uploads/                  # User-uploaded project images (gitignored)
├── storage/
│   ├── cache/            # Response cache (gitignored)
│   └── rate_limit/       # Rate limit counters (gitignored)
├── .env.example
├── composer.json
└── README.md
```

---

## ✦ Admin Panel

| Route | Purpose |
|-------|---------|
| `/admin/login` | Sign in |
| `/admin` | Dashboard |
| `/admin/settings` | Site name, tagline, canonical URL, SEO defaults, social links |
| `/admin/projects` | List, create, edit, delete projects |
| `/admin/categories` | Manage categories |
| `/admin/tags` | Manage tags |

> Admin routes are guarded by `AdminGuard` — session + DB role check on every request.

---

## ✦ Security Notes

- Passwords hashed with `PASSWORD_BCRYPT` (cost 12)
- CSRF tokens verified on every POST via `hash_equals`
- Rate limiter on login (file-based, no Redis required)
- `HSTS` and `CSP` headers set in `public/index.php`
- DB errors hidden behind `APP_DEBUG` flag
- Uploads re-processed through GD (no raw file writes) + `.htaccess` blocks PHP execution inside `/uploads/`

---

## ✦ Hosting

This portfolio runs on a **free PHP hosting account** at [**Anybusiness.ro**](https://anybusiness.ro) — zero monthly cost, MySQL included, Apache + `.htaccess` routing.

> [Anybusiness.ro](https://anybusiness.ro) — free website hosting for Romanian businesses and individuals.

---

## ✦ License

MIT — use this as a base for your own portfolio, adapt freely.

---

<div align="center">

Made with care · Powered by [Anybusiness.ro](https://anybusiness.ro) free hosting

</div>
