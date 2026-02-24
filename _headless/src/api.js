/**
 * API Module
 * Handles all requests to the WordPress REST API
 */

// If you are developing locally, you might want to point this to your local WordPress site
// like 'http://xyloo.test' instead of the production 'https://xyloo.es'
const BASE_URL = 'https://xyloo.es';
const API_NAMESPACE = '/wp-json/wp/v2';
const CUSTOM_NAMESPACE = '/wp-json/xarop/v1';

export async function fetchSiteInfo() {
    try {
        const response = await fetch(`${BASE_URL}/wp-json`);
        if (!response.ok) throw new Error('Failed to fetch site info');
        return await response.json();
    } catch (error) {
        console.error('Error fetching site info:', error);
        return { name: 'Xarop', description: '' };
    }
}

export async function fetchMenus() {
    try {
        const response = await fetch(`${BASE_URL}${CUSTOM_NAMESPACE}/menus`);
        if (!response.ok) {
            console.warn(`Menus endpoint ${CUSTOM_NAMESPACE}/menus not found on ${BASE_URL}. Using fallback.`);
            return null;
        }
        return await response.json();
    } catch (error) {
        console.error('Error fetching menus:', error);
        return null;
    }
}

export async function fetchAllPages() {
    try {
        const response = await fetch(`${BASE_URL}${API_NAMESPACE}/pages?per_page=100&_fields=id,title,link,slug,parent`);
        if (!response.ok) throw new Error('Failed to fetch pages');
        return await response.json();
    } catch (error) {
        console.error('Error fetching all pages:', error);
        return [];
    }
}

export async function fetchSliderPages() {
    try {
        // We use _embed to get featured images
        const response = await fetch(`${BASE_URL}${API_NAMESPACE}/pages?per_page=10&_embed`);
        if (!response.ok) throw new Error('Failed to fetch slider pages');
        const pages = await response.json();
        console.log('Slider pages fetched:', pages); // Debug slider data
        return pages;
    } catch (error) {
        console.error('Error fetching slider pages:', error);
        return [];
    }
}

export async function fetchCategories() {
    try {
        const response = await fetch(`${BASE_URL}${API_NAMESPACE}/categories?hide_empty=true`);
        if (!response.ok) throw new Error('Failed to fetch categories');
        return await response.json();
    } catch (error) {
        console.error('Error fetching categories:', error);
        return [];
    }
}

export async function fetchFilteredPosts(categoryId = 'all') {
    try {
        const url = `${BASE_URL}${CUSTOM_NAMESPACE}/filtered-posts?category=${categoryId}&per_page=12`;
        const response = await fetch(url);
        if (!response.ok) return [];
        const data = await response.json();
        return data.posts || [];
    } catch (error) {
        return [];
    }
}

export async function fetchPostBySlug(slug) {
    try {
        const response = await fetch(`${BASE_URL}${API_NAMESPACE}/posts?slug=${slug}&_embed`);
        if (!response.ok) throw new Error('Failed to fetch post');
        const posts = await response.json();
        return posts[0] || null;
    } catch (error) {
        console.error('Error fetching post by slug:', error);
        return null;
    }
}

export async function fetchPageBySlug(slug) {
    try {
        const response = await fetch(`${BASE_URL}${API_NAMESPACE}/pages?slug=${slug}&_embed`);
        if (!response.ok) throw new Error('Failed to fetch page');
        const pages = await response.json();
        return pages[0] || null;
    } catch (error) {
        console.error('Error fetching page by slug:', error);
        return null;
    }
}
