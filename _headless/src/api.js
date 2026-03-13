/**
 * Xarop Headless — Módulo de API
 *
 * Gestiona todas las peticiones a la REST API de WordPress.
 * Configurado para funcionar con el Modo Headless de xarop-theme v2.
 *
 * ─────────────────────────────────────────────────────────────────────────
 * CONFIGURACIÓN:
 * Cambia BASE_URL por la URL de tu instalación de WordPress.
 * En desarrollo local puedes usar: 'http://localhost/wordpress'
 *                                   'http://mi-proyecto.test'
 * ─────────────────────────────────────────────────────────────────────────
 *
 * ENDPOINTS DISPONIBLES (xarop-theme v2):
 *
 *   Estándar WordPress:
 *     GET /wp-json/wp/v2/pages?_embed          → Páginas con imágenes
 *     GET /wp-json/wp/v2/posts?_embed          → Entradas con imágenes
 *     GET /wp-json/wp/v2/categories            → Taxonomía categorías
 *     GET /wp-json                              → Info del sitio
 *
 *   Personalizados Xarop:
 *     GET /wp-json/xarop/v1/menus              → Menús de navegación
 *     GET /wp-json/xarop/v1/filtered-posts     → Posts filtrados por cat.
 *       Parámetros: ?category=ID&per_page=12
 *
 *   Campos extra en pages y posts (via register_rest_field):
 *     custom_gallery     → { ids[], images[{id, full, large, medium, thumbnail, alt}] }
 *     shared_categories  → [{ id, name, slug, description, count, link }]
 *
 * @package Xarop_Theme
 * @since   2.0.0
 */

// ─────────────────────────────────────────────────────────────────────────
// CONFIGURACIÓN — edita solo estas líneas
// ─────────────────────────────────────────────────────────────────────────

const BASE_URL        = 'https://xyloo.es';   // URL de tu WordPress
const API_NAMESPACE   = '/wp-json/wp/v2';
const CUSTOM_NAMESPACE = '/wp-json/xarop/v1';

// ─────────────────────────────────────────────────────────────────────────
// UTILIDADES INTERNAS
// ─────────────────────────────────────────────────────────────────────────

/**
 * Wrapper fetch con manejo de errores centralizado.
 * @param {string} url
 * @param {RequestInit} [options]
 * @returns {Promise<any>}
 */
async function apiFetch( url, options = {} ) {
    const response = await fetch( url, {
        headers: { 'Accept': 'application/json' },
        ...options,
    } );
    if ( ! response.ok ) {
        throw new Error( `API error ${response.status}: ${url}` );
    }
    return response.json();
}

// ─────────────────────────────────────────────────────────────────────────
// ENDPOINTS ESTÁNDAR WORDPRESS
// ─────────────────────────────────────────────────────────────────────────

/** Información general del sitio (nombre, descripción, URL…). */
export async function fetchSiteInfo() {
    try {
        return await apiFetch( `${BASE_URL}/wp-json` );
    } catch {
        return { name: 'Xarop', description: '' };
    }
}

/** Todas las páginas (solo campos necesarios para menú y enrutamiento). */
export async function fetchAllPages() {
    try {
        return await apiFetch(
            `${BASE_URL}${API_NAMESPACE}/pages?per_page=100&_fields=id,title,link,slug,parent`
        );
    } catch {
        return [];
    }
}

/**
 * Páginas para el slider hero (con imágenes embebidas).
 * @param {number} [perPage=8]
 */
export async function fetchSliderPages( perPage = 8 ) {
    try {
        return await apiFetch(
            `${BASE_URL}${API_NAMESPACE}/pages?per_page=${perPage}&_embed`
        );
    } catch {
        return [];
    }
}

/** Página individual por slug (con imágenes embebidas). */
export async function fetchPageBySlug( slug ) {
    try {
        const pages = await apiFetch(
            `${BASE_URL}${API_NAMESPACE}/pages?slug=${encodeURIComponent( slug )}&_embed`
        );
        return pages[0] || null;
    } catch {
        return null;
    }
}

/** Post individual por slug (con imágenes embebidas). */
export async function fetchPostBySlug( slug ) {
    try {
        const posts = await apiFetch(
            `${BASE_URL}${API_NAMESPACE}/posts?slug=${encodeURIComponent( slug )}&_embed`
        );
        return posts[0] || null;
    } catch {
        return null;
    }
}

/** Todas las categorías no vacías. */
export async function fetchCategories() {
    try {
        return await apiFetch( `${BASE_URL}${API_NAMESPACE}/categories?hide_empty=true&per_page=50` );
    } catch {
        return [];
    }
}

// ─────────────────────────────────────────────────────────────────────────
// ENDPOINTS PERSONALIZADOS XAROP
// ─────────────────────────────────────────────────────────────────────────

/**
 * Menús de navegación del sitio.
 * Devuelve un objeto con las locations como keys:
 *   { 'main-menu': [{id, title, url, children:[]}], 'footer-menu': [...] }
 *
 * Retorna null si el endpoint no está disponible.
 */
export async function fetchMenus() {
    try {
        return await apiFetch( `${BASE_URL}${CUSTOM_NAMESPACE}/menus` );
    } catch {
        console.warn( '[Xarop API] Endpoint /menus no disponible. Usando menú de fallback.' );
        return null;
    }
}

/**
 * Posts filtrados por categoría con paginación.
 *
 * @param {string|number} [categoryId='all']
 * @param {number}        [perPage=12]
 * @returns {Promise<Array>} Array de posts.
 *
 * Estructura de cada post:
 *   { id, title, excerpt, link, thumbnail: {url, width, height}, categories: [{id, name, slug}] }
 */
export async function fetchFilteredPosts( categoryId = 'all', perPage = 12 ) {
    try {
        const url = `${BASE_URL}${CUSTOM_NAMESPACE}/filtered-posts`
            + `?category=${categoryId}&per_page=${perPage}`;
        const data = await apiFetch( url );
        return data.posts || [];
    } catch {
        return [];
    }
}
