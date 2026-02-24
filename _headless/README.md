# Xarop Headless Web 🚀

Esta es la versión **headless** del tema Xarop, diseñada para ser rápida, moderna y totalmente desacoplada de WordPress, utilizando la **REST API** para la gestión de contenidos.

## ✨ Características Premium

- **Arquitectura Headless**: Comunicación directa con los endpoints de WordPress (`wp-json`).
- **Diseño Glassmorphism**: Header pegajoso con efectos de desenfoque y profundidad.
- **Slider Dinámico Interactivo**: Carrusel de imágenes que consume páginas destacadas automáticamente.
- **Grid de Posts con Filtrado Real-time**: Sistema de filtrado por categorías sin recarga de página.
- **Optimizado con Vite**: Tooling moderno para un desarrollo ultra rápido y builds optimizados.
- **Aesthetics Modernas**: Tipografía Inter, paleta de colores armoniosa y micro-interacciones.

## 🛠️ Stack Tecnológico

- **Frontend**: HTML5, Vanilla CSS (Custom Properties), JavaScript ES6+.
- **Build Tool**: [Vite](https://vitejs.dev/)
- **Backend**: WordPress REST API (Endpoints estándar y personalizados de Xarop).

## 🚀 Instalación y Uso

Asegúrate de tener [Node.js](https://nodejs.org/) instalado.

1.  **Entrar al directorio**:

    ```bash
    cd themes/xarop-theme/_headless
    ```

2.  **Instalar dependencias**:

    ```bash
    npm install
    ```

3.  **Desarrollo**:

    ```bash
    npm run dev
    ```

    _La web será accesible en `http://localhost:5173` (o similar)._

4.  **Producción**:
    ```bash
    npm run build
    ```
    _Los archivos optimizados se generarán en la carpeta `dist/`._

## 🔌 Configuración de API

El archivo `src/api.js` está configurado para detectar automáticamente tu entorno:

- Si detecta `localhost`, apuntará a `http://xyloo.local`.
- En producción, utilizará la URL relativa del dominio donde esté alojado.

## 📁 Estructura del Proyecto

```text
_headless/
├── index.html         # Punto de entrada y estructura base
├── src/
│   ├── main.js        # Lógica de la app, Slider y Filtros
│   ├── api.js         # Módulo de comunicación con WordPress
│   └── style.css      # Sistema de diseño y estilos premium
├── package.json       # Scripts y dependencias
└── README.md          # Esta guía
```

---

Creado con ❤️ para la experiencia **Xarop Headless**.
