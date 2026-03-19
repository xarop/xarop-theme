# 🚀 Xarop Theme — WordPress Starter Kit (v2.0)

Modular, ultra-lightweight, multipurpose WordPress starter theme. 
**Preparado para WordPress 7.0 (Multiidioma Nativo) y Gutenberg de alto rendimiento.**

---

## 🏗️ Filosofía del Proyecto
Xarop no es solo un tema; es una metodología de desarrollo que separa el **Sistema de Diseño** (controlado por ti en el código) de la **Gestión de Contenidos** (fácil para el cliente).

- **Sin Bloatware:** Ni Elementor, ni Divi. Solo bloques nativos.
- **Configuración Centralizada:** Todo el estilo se define en `theme-config.php`.
- **Developer-First:** Pensado para usarse con VS Code, terminal y entornos Playground.

---

## ⚡ Demo Instantánea (WordPress Playground)

¿Quieres probar el tema sin instalar nada? Haz clic en el siguiente enlace para abrir una instancia real de WordPress en tu navegador con el tema ya configurado:

👉 [**Ejecutar Demo en Vivo (Blueprint)**](https://playground.wordpress.net/?blueprint_url=https://raw.githubusercontent.com/xarop/xarop-theme/main/blueprint.json)

*El Blueprint configura automáticamente un usuario Editor (`cliente_demo` / `cliente_password`) para que pruebes la experiencia del cliente.*

---

## 🎨 Configuración en 1 Minuto (`theme-config.php`)

Centralizamos los **Design Tokens** en un solo lugar. Los cambios se inyectan como variables CSS en el frontend y se reflejan en el editor de bloques.

```php
'colors' => [
    'primary'       => '#ee2455',   // Tu color de marca
    'text'          => '#1e293b',
    'bg'            => '#ffffff',
],
'typography' => [
    'font_primary' => "'Inter', sans-serif",
    'size_base'    => '17px',
],
'modules' => [
    'gutenberg_enabled' => true,
    'headless_mode'     => false, // Convierte WP en una API pura
],