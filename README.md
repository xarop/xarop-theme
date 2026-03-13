# Xarop Theme — WordPress Starter Kit

Modular, ultra-lightweight, multipurpose WordPress starter theme. Centralised configuration in a single file. No heavy frameworks. Performance-first.

---

## Change the visual identity in 1 minute

Open **`theme-config.php`** and edit the `colors` and `typography` sections:

```php
'colors' => [
    'primary'       => '#ee2455',   // ← Your brand colour
    'primary_dark'  => '#b01a3e',
    'primary_light' => '#ff5c7a',
    'text'          => '#1e293b',
    'bg'            => '#ffffff',
    // ...
],

'typography' => [
    // For Google Fonts: add the @import in assets/css/style.css
    // and set the font name here.
    'font_primary' => "'Inter', sans-serif",
    'font_heading' => "'Poppins', sans-serif",
    'size_base'    => '17px',
],
```

Save the file. Values are injected as CSS variables in the `<head>` on every page load. No compiling, no cache clearing.

---

## WordPress Customizer

All of the above is also available via **Appearance → Customize → Xarop Theme**, without touching any file.

The Customizer panel contains four sections:

### Colors
Live colour pickers for every CSS variable:

| Setting | CSS variable |
|---|---|
| Primary colour | `--color-primary` |
| Primary dark (hover) | `--color-primary-dark` |
| Primary light (highlights) | `--color-primary-light` |
| Main text | `--color-text` |
| Secondary text | `--color-text-light` |
| Base background | `--color-bg` |
| Alternative background | `--color-bg-alt` |
| Borders & dividers | `--color-border` |

Changes preview **instantly** in the Customizer (live `postMessage` transport — no page reload needed).

### Typography
Text fields for the font stacks and size values:

| Setting | Accepts |
|---|---|
| Body font | Any CSS font-family value or Google Fonts name |
| Heading font | Any CSS font-family value or Google Fonts name |
| Monospace font | Any CSS font-family value |
| Base size | Any CSS size unit (`16px`, `1rem`…) |
| Line height | Unitless ratio (`1.6`) or any CSS value |

> **Google Fonts tip:** add the `@import` line in `assets/css/style.css` first, then paste the font name in the Customizer field.

### Features (module toggles)
Checkboxes that mirror the `theme-config.php` boolean flags:

| Toggle | On | Off |
|---|---|---|
| Block Editor (Gutenberg) | Visual editor active | Gutenberg disabled, its CSS removed from the front |
| Blog / Posts | Posts section visible in admin | Posts menu hidden from admin |
| Comments system | Comments active with template & styles | Comments removed from core, admin and toolbar |
| Headless mode | Redirects front-end to admin, enables CORS REST API | Standard WordPress behaviour |
| Animations | Loads `animations.css` + `animations.js` | Zero animation overhead |

### Maintenance Mode
Two fields that appear only when the **Enable maintenance mode** checkbox is ticked:

| Field | Purpose |
|---|---|
| Page title | Heading shown to logged-out visitors |
| Visitor message | Body text / HTML shown below the title |

> Only logged-in users can access the site while maintenance mode is active.

### Priority rules
Customizer values **always override** `theme-config.php`. Use `theme-config.php` as the hard-coded baseline and the Customizer for client-facing tweaks that may change over time.

---

## Modules (enable / disable)

| Option in `theme-config.php` | `true` | `false` |
|---|---|---|
| `gutenberg_enabled` | Block editor active | Gutenberg disabled, its CSS removed from the front |
| `blog_enabled` | Posts section visible in admin | Posts menu hidden from admin |
| `comments_enabled` | Comments active with template & styles | Comments removed from core, admin and toolbar |
| `headless_mode` | Redirects front-end to admin, enables CORS REST API | Standard WordPress behaviour |
| `animations_enabled` | Loads `animations.css` + `animations.js` | Zero animation overhead |
| `private_site` | Only logged-in users can see the site | Site publicly accessible |

---

## Custom Post Type Generator

```php
'custom_post_types' => [
    [
        'slug'     => 'project',
        'singular' => 'Project',
        'plural'   => 'Projects',
        'icon'     => 'dashicons-portfolio',
        'public'   => true,
        'rest'     => true,   // Exposes the CPT in the REST API
    ],
    [
        'slug'     => 'service',
        'singular' => 'Service',
        'plural'   => 'Services',
        'icon'     => 'dashicons-admin-tools',
    ],
],
```

Each CPT is registered with shared `category` taxonomy support, REST API exposure, and Gutenberg (when enabled).

---

## Animation System

With `animations_enabled: true`, add these classes to any element:

```html
<!-- Fade in on viewport entry -->
<div class="animate-on-scroll">...</div>

<!-- Direction variants -->
<div class="animate-on-scroll reveal-up">...</div>
<div class="animate-on-scroll reveal-left">...</div>
<div class="animate-on-scroll reveal-right">...</div>
<div class="animate-on-scroll scale-in">...</div>

<!-- Staggered delay -->
<div class="animate-on-scroll reveal-up delay-2">...</div>

<!-- Automatic cascade for grids / lists -->
<ul class="animate-stagger">
    <li>...</li>  <!-- delay 50 ms -->
    <li>...</li>  <!-- delay 100 ms -->
    <li>...</li>  <!-- delay 150 ms -->
</ul>
```

Respects `prefers-reduced-motion` for accessibility.

---

## Headless Mode

With `headless_mode: true`:

1. WordPress acts as a CMS back-end only.
2. The front-end lives in `_headless/` (Vite + Vanilla JS) or an external Next.js repo.
3. See `_headless/README.md` for full installation and deployment instructions.

---

## File structure

```
xarop-theme/
├── theme-config.php          ← START HERE: all configuration
├── functions.php             ← Loads modules according to the config
├── comments.php              ← Comments template (HTML)
│
├── inc/
│   ├── cleanup.php           ← Gutenberg, emojis, embeds, clean <head>
│   ├── comments.php          ← Comment logic (enable/disable)
│   ├── customizer.php        ← WordPress Customizer panel & controls
│   ├── headless.php          ← Headless mode + CORS
│   ├── meta-boxes.php        ← Custom meta boxes
│   ├── private.php           ← Maintenance / private-site mode
│   ├── rest-api.php          ← REST API extensions (gallery, categories, menus)
│   ├── ajax-grid.php         ← Paginated AJAX grid
│   └── xarop.php             ← Creator customisation (DO NOT EDIT)
│
├── assets/
│   ├── css/
│   │   ├── variables.css     ← Base CSS variables (overridden by config)
│   │   ├── style.css         ← Main stylesheet
│   │   ├── layout.css        ← Structure & layout
│   │   ├── animations.css    ← Animation layer (conditional)
│   │   ├── comments.css      ← Comment styles (conditional)
│   │   └── ...
│   └── js/
│       ├── main.js           ← Main JavaScript
│       ├── animations.js     ← IntersectionObserver (conditional)
│       └── lightbox.js       ← Gallery lightbox
│
└── _headless/                ← Decoupled front-end (Vite + Vanilla JS)
    ├── src/api.js            ← REST API module
    ├── src/main.js           ← SPA: router, components, logic
    └── README.md             ← Installation & deployment guide
```

---

## Requirements

- WordPress 6.0+
- PHP 8.0+
- Node.js 18+ (only for `_headless/`)

---

Created by [xarop.com](https://xarop.com)
