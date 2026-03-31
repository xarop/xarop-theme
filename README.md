# 🚀 Xarop Theme — El WordPress Starter Kit Definitivo

**Xarop** es un tema base modular, ultra-ligero y multipropósito. Diseñado para desarrolladores que buscan el máximo rendimiento sin la carga de frameworks pesados o constructores visuales lentos.

---

## ⚡ Demo Instantánea (Sin instalación)

Prueba el potencial de Xarop directamente en tu navegador. WordPress real, configuración real, cero latencia.

[![Probar Demo](https://img.shields.io/badge/PROBAR_DEMO_EN_VIVO-Click_Aquí-ee2455?style=for-the-badge&logo=wordpress)](https://playground.wordpress.net/?blueprint_url=https://raw.githubusercontent.com/xarop/xarop-theme/main/blueprint.json&theme_url=https://github.com/xarop/xarop-theme/archive/refs/heads/main.zip&plugin=gutenberg)

*Al abrir la demo, el Blueprint configurará automáticamente el entorno con los colores corporativos de xarop.com y preparará el editor de bloques.*

---

## 🏗️ Filosofía "Developer-First"

Xarop separa el **Sistema de Diseño** (Tokens) de la **Gestión de Contenidos**. Tú controlas el código, el cliente controla los bloques.

- **Configuración Centralizada:** Controla colores, tipografías y módulos desde un único archivo: `theme-config.php`.
- **WordPress 7 Ready:** Preparado para el sistema de **Multiidioma Nativo** y la Fase 4 de Gutenberg.
- **Rendimiento Extremo:** Carga solo lo que necesitas. Sin jQuery, sin CSS innecesario.
- **Headless Mode:** Activa el flag en el config y convierte WordPress en una API pura para React/Next.js en un segundo.

---

## 🎨 Configuración Rápida (`theme-config.php`)

Cambia la identidad visual de tu sitio editando el array de configuración. No necesitas compilar SASS ni purgar caché.

```php
'colors' => [
    'primary'       => '#ee2455',   // Color principal (Xarop Pink)
    'primary_dark'  => '#b01a3e',
    'text'          => '#1e293b',
    'bg'            => '#ffffff',
],

'typography' => [
    'font_primary' => "'Inter', sans-serif",
    'size_base'    => '17px',
],

'modules' => [
    'gutenberg_enabled' => true,    // Soporte total para bloques nativos
    'headless_mode'     => false,   // Redirección automática y CORS habilitado
    'animations'        => true,    // Sistema de animaciones On-Scroll incluido
],