# Xarop Headless — Front-end Desacoplado 🚀

Front-end independiente construido con **Vite + Vanilla JS** que consume el CMS WordPress a través de la REST API. Funciona en combinación con el **Modo Headless** de `xarop-theme v2`.

---

## Arquitectura

```
┌─────────────────────────────┐         REST API          ┌───────────────────────────┐
│   WordPress + xarop-theme   │  ─────────────────────►  │    _headless/ (este dir)  │
│   headless_mode: true       │   /wp-json/wp/v2/         │    Vite + Vanilla JS      │
│   Solo back-end / CMS       │   /wp-json/xarop/v1/      │    Vercel, Netlify, etc.  │
└─────────────────────────────┘                           └───────────────────────────┘
```

Para activar el modo headless en WordPress, edita `theme-config.php`:

```php
'headless_mode' => true,
```

Esto redirige todo el tráfico del front-end al `/wp-admin` y activa las cabeceras CORS.

---

## Instalación rápida

Requiere Node.js 18+.

```bash
cd wp-content/themes/xarop-theme/_headless
npm install
npm run dev        # http://localhost:5173
npm run build      # Carpeta dist/ lista para deploy
npm run preview    # Previsualizar el build
```

---

## Configuración de la API

Edita **`src/api.js`** — solo una línea:

```js
const BASE_URL = 'https://tu-wordpress.com';
```

---

## Endpoints disponibles

### Estándar WordPress

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/wp-json` | Info del sitio |
| GET | `/wp-json/wp/v2/pages?_embed` | Páginas con imágenes |
| GET | `/wp-json/wp/v2/posts?_embed` | Entradas con imágenes |
| GET | `/wp-json/wp/v2/categories` | Categorías |
| GET | `/wp-json/wp/v2/pages?slug=SLUG&_embed` | Página por slug |
| GET | `/wp-json/wp/v2/posts?slug=SLUG&_embed` | Post por slug |

### Personalizados Xarop

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/wp-json/xarop/v1/menus` | Menús con jerarquía |
| GET | `/wp-json/xarop/v1/filtered-posts?category=ID&per_page=12` | Posts filtrados |

### Campos extra en posts y páginas

```json
{
  "custom_gallery": {
    "ids": [12, 34],
    "images": [{ "id": 12, "full": {"url":"...","width":1920,"height":1080},
                 "medium": {...}, "thumbnail": {...}, "alt": "...", "caption": "..." }]
  },
  "shared_categories": [
    { "id": 5, "name": "Proyectos", "slug": "proyectos", "count": 12 }
  ]
}
```

---

## Despliegue

### Vercel / Netlify

1. Directorio raíz: `wp-content/themes/xarop-theme/_headless`
2. Build command: `npm run build`
3. Output directory: `dist`

### Apache (.htaccess para SPA)

```apache
RewriteEngine On
RewriteRule ^index\.html$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.html [L]
```

### Nginx

```nginx
location / { try_files $uri $uri/ /index.html; }
```

---

## CORS en WordPress

Con `headless_mode: true`, el tema añade automáticamente cabeceras CORS.
Para restringir a un dominio concreto en producción, edita `inc/headless.php` línea ~70:

```php
header( 'Access-Control-Allow-Origin: https://tu-frontend.com' );
```

---

Creado con corazón por [xarop.com](https://xarop.com)
