/**
 * Xarop Theme — Animaciones con IntersectionObserver
 *
 * Observa los elementos con clase .animate-on-scroll y .animate-stagger
 * y añade la clase .is-visible cuando entran en el viewport.
 *
 * Sin dependencias externas. Peso: ~1KB minificado.
 * Solo se carga si 'animations_enabled' es true en theme-config.php.
 *
 * Uso en plantillas PHP:
 *   <div class="animate-on-scroll reveal-up">Contenido</div>
 *   <ul class="animate-stagger"> <li>...</li> <li>...</li> </ul>
 *
 * @package Xarop_Theme
 * @since   2.0.0
 */

( function () {

    'use strict';

    /**
     * Configuración del observador.
     * threshold: qué porcentaje del elemento debe ser visible para disparar.
     * rootMargin: margen extra (permite animar antes de entrar al viewport).
     */
    const OBSERVER_OPTIONS = {
        threshold:  0.12,
        rootMargin: '0px 0px -40px 0px',
    };

    /**
     * Inicializa el IntersectionObserver para todos los elementos animables.
     */
    function initAnimations() {

        // Comprueba soporte del navegador (IE11 no lo soporta, pero es irrelevante en 2025).
        if ( ! ( 'IntersectionObserver' in window ) ) {
            // Fallback: muestra todos los elementos directamente.
            document.querySelectorAll( '.animate-on-scroll, .animate-stagger' ).forEach( function ( el ) {
                el.classList.add( 'is-visible' );
            } );
            return;
        }

        var observer = new IntersectionObserver( handleIntersection, OBSERVER_OPTIONS );

        // Observar elementos individuales.
        document.querySelectorAll( '.animate-on-scroll' ).forEach( function ( el ) {
            observer.observe( el );
        } );

        // Observar contenedores stagger.
        document.querySelectorAll( '.animate-stagger' ).forEach( function ( el ) {
            observer.observe( el );
        } );
    }

    /**
     * Callback del observer.
     * Añade .is-visible y deja de observar el elemento (one-shot).
     *
     * @param {IntersectionObserverEntry[]} entries
     * @param {IntersectionObserver}        observer
     */
    function handleIntersection( entries, observer ) {
        entries.forEach( function ( entry ) {
            if ( entry.isIntersecting ) {
                entry.target.classList.add( 'is-visible' );
                // Dejamos de observar para no disparar de nuevo al hacer scroll inverso.
                observer.unobserve( entry.target );
            }
        } );
    }

    /**
     * Anima automáticamente los elementos dentro de .entry-content.
     * Aplica reveal-up a párrafos, imágenes y elementos de lista
     * si no tienen ya una clase de animación asignada.
     *
     * Solo se ejecuta si xaropAnimations.autoAnimate es true
     * (configurado desde wp_localize_script en functions.php).
     */
    function autoAnimateContent() {
        var contentAreas = document.querySelectorAll( '.entry-content' );

        contentAreas.forEach( function ( area ) {
            var selectors = 'p, blockquote, figure, .wp-block-image, h2, h3, h4';
            area.querySelectorAll( selectors ).forEach( function ( el, index ) {
                if ( ! el.classList.contains( 'animate-on-scroll' ) ) {
                    el.classList.add( 'animate-on-scroll', 'reveal-up' );
                    // Delay escalonado sutil para los primeros 5 elementos.
                    if ( index < 5 ) {
                        el.classList.add( 'delay-' + ( index + 1 ) );
                    }
                }
            } );
        } );
    }

    /**
     * Punto de entrada.
     * Espera al DOM y luego inicializa.
     */
    function ready() {
        // Auto-animación del contenido de artículos si está configurado.
        if ( window.xaropAnimations && window.xaropAnimations.autoAnimate ) {
            autoAnimateContent();
        }
        initAnimations();
    }

    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', ready );
    } else {
        // El DOM ya está listo (script en footer con defer).
        ready();
    }

} )();
