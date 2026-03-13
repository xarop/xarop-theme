# Xarop Theme v2 — WordPress Starter Kit

Tema starter de WordPress modular, ultra-ligero y multipropósito. Configuración centralizada en un solo archivo. Sin frameworks pesados. Orientado al rendimiento.

---

## Cambia la identidad visual en 1 minuto

Abre **`theme-config.php`** y edita la sección `colors` y `typography`:

```php
'colors' => [
    'primary'       => '#ee2455',   // ← Tu color de marca
    'primary_dark'  => '#b01a3e',
    'primary_light' => '#ff5c7a',
    'text'          => '#1e293b',
    'bg'            => '#ffffff',
    // ...
],

'typography' => [
    // Para Google Fonts: añade el @import en assets/css/style.css
    // y pon aquí el nombre de la fuente.
    'font_primary' => "'Inter', sans-serif",
    'font_heading' => "'Poppins', sans-serif",
    'size_base'    => '17px',
],
```

Guarda el archivo. Los valores se inyectan como variables CSS en el `<head>` en cada carga de página. Sin compilar, sin vaciar caché.

---

## Módulos activables / desactivables

| Opción en `theme-config.php` | `true` | `false` |
|---|---|---|
| `gutenberg_enabled` | Editor de bloques activo | Desactiva Gutenberg y elimina sus CSS del front |
| `blog_enabled` | Sección "Entradas" visible en el admin | Oculta el menú Entradas del admin |
| `comments_enabled` | Comentarios activos con plantilla y estilos | Elimina comentarios del core, admin y barra superior |
| `headless_mode` | Redirige el front al admin, activa CORS REST API | WordPress estándar |
| `animations_enabled` | Carga `animations.css` + `animations.js` | Sin overhead de animaciones |

---

## Generador de Custom Post Types

```php
'custom_post_types' => [
    [
        'slug'     => 'project',
        'singular' => 'Proyecto',
        'plural'   => 'Proyectos',
        'icon'     => 'dashicons-portfolio',
        'public'   => true,
        'rest'     => true,   // Expone el CPT en la REST API
    ],
    [
        'slug'     => 'service',
        'singular' => 'Servicio',
        'plural'   => 'Servicios',
        'icon'     => 'dashicons-admin-tools',
    ],
],
```

Cada CPT se registra con soporte para taxonomía `category` compartida, REST API, y Gutenberg (si está activo).

---

## Sistema de Animaciones

Con `animations_enabled: true`, añade estas clases a cualquier elemento:

```html
<!-- Fade in al entrar en el viewport -->
<div class="animate-on-scroll">...</div>

<!-- Variantes de dirección -->
<div class="animate-on-scroll reveal-up">...</div>
<div class="animate-on-scroll reveal-left">...</div>
<div class="animate-on-scroll reveal-right">...</div>
<div class="animate-on-scroll scale-in">...</div>

<!-- Con delay escalonado -->
<div class="animate-on-scroll reveal-up delay-2">...</div>

<!-- Grid/lista en cascada automática -->
<ul class="animate-stagger">
    <li>...</li>  <!-- delay 50ms -->
    <li>...</li>  <!-- delay 100ms -->
    <li>...</li>  <!-- delay 150ms -->
</ul>
```

Respeta `prefers-reduced-motion` para accesibilidad.

---

## Modo Headless

Con `headless_mode: true`:

1. WordPress actúa solo como CMS (back-end).
2. El front-end se desarrolla en `_headless/` (Vite + Vanilla JS) o en un repositorio Next.js externo.
3. Ver `_headless/README.md` para instrucciones completas de instalación y despliegue.

---

## Estructura de archivos

```
xarop-theme/
├── theme-config.php          ← EMPIEZA AQUÍ: toda la configuración
├── functions.php             ← Carga módulos según el config
├── comments.php              ← Plantilla de comentarios (HTML)
│
├── inc/
│   ├── cleanup.php           ← Gutenberg, emojis, embeds, head limpio
│   ├── comments.php          ← Lógica de comentarios (enable/disable)
│   ├── headless.php          ← Modo headless + CORS
│   ├── cpt-generator.php     ← Registro de CPTs desde el config
│   ├── post-types.php        ← CPTs específicos del proyecto
│   ├── meta-boxes.php        ← Meta boxes personalizados
│   ├── rest-api.php          ← Extensiones REST API (gallery, categories, menus)
│   ├── ajax-grid.php         ← Grid paginado con AJAX
│   └── xarop.php             ← Personalización del creador (NO EDITAR)
│
├── assets/
│   ├── css/
│   │   ├── variables.css     ← Variables CSS base (el config las sobreescribe)
│   │   ├── style.css         ← Estilos principales
│   │   ├── layout.css        ← Estructura y layout
│   │   ├── animations.css    ← Capa de animaciones (condicional)
│   │   ├── comments.css      ← Estilos de comentarios (condicional)
│   │   └── ...
│   └── js/
│       ├── main.js           ← JavaScript principal
│       ├── animations.js     ← IntersectionObserver (condicional)
│       └── lightbox.js       ← Lightbox de galería
│
└── _headless/                ← Front-end desacoplado (Vite + Vanilla JS)
    ├── src/api.js            ← Módulo REST API
    ├── src/main.js           ← SPA: router, componentes, lógica
    └── README.md             ← Guía de instalación y despliegue
```

---

## Requisitos

- WordPress 6.0+
- PHP 8.0+
- Node.js 18+ (solo para `_headless/`)

---

Creado por [xarop.com](https://xarop.com)
