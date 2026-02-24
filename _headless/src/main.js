import './style.css';
import page from 'page';
import { 
    fetchSiteInfo,
    fetchMenus,
    fetchAllPages,
    fetchSliderPages, 
    fetchCategories, 
    fetchFilteredPosts, 
    fetchPostBySlug, 
    fetchPageBySlug 
} from './api.js';

/**
 * App State
 */
const state = {
    currentCategory: 'all',
    sliderIndex: 0,
    sliderInterval: null,
    menus: {},
    siteInfo: {}
};

/**
 * DOM Elements
 */
const app = document.getElementById('app');
const primaryMenuUI = document.getElementById('primary-menu');
const footerMenuUI = document.getElementById('footer-menu-list');
const footerInfoUI = document.getElementById('footer-info');
const siteTitleUI = document.querySelector('.site-title');
const siteTaglineUI = document.querySelector('.site-tagline');

/**
 * Helpers
 */
function showLoader() {
    app.innerHTML = '<div class="loader-container"><div class="loader"></div></div>';
}

function updateActiveMenu(path) {
    document.querySelectorAll('.main-navigation a').forEach(link => {
        const href = link.getAttribute('href');
        link.classList.toggle('active', href === path);
    });
}

function getFeaturedImage(data) {
    if (!data) return '';
    const embedded = data._embedded;
    if (embedded && embedded['wp:featuredmedia'] && embedded['wp:featuredmedia'][0]) {
        const media = embedded['wp:featuredmedia'][0];
        if (media.source_url) return media.source_url;
    }
    // Fallback if featured image is restricted but we have a custom field or similar
    return '';
}

/**
 * Menu & Site Rendering
 */
function renderSiteInfo(info) {
    if (!info) return;
    if (siteTitleUI) siteTitleUI.innerText = 'XYLOO';
    if (siteTaglineUI) siteTaglineUI.innerText = info.description || '';
    document.title = 'XYLOO' + (info.description ? ' | ' + info.description : '');
}

function renderMenu(menuItems, container) {
    if (!container) return;
    
    if (!menuItems || (Array.isArray(menuItems) && menuItems.length === 0)) {
        return false;
    }
    
    container.innerHTML = menuItems.map(item => `
        <li class="menu-item ${item.children?.length ? 'has-children' : ''}">
            <a href="${item.url || '/'}" data-nav>${item.title}</a>
            ${item.children?.length ? `
                <ul class="sub-menu">
                    ${item.children.map(child => `
                        <li><a href="${child.url || '/'}" data-nav>${child.title}</a></li>
                    `).join('')}
                </ul>
            ` : ''}
        </li>
    `).join('');

    attachNavEvents(container);
    return true;
}

function renderFallbackMenu(pages, container) {
    if (!container || !pages || pages.length === 0) return;
    
    const pageMap = {};
    pages.forEach(p => {
        pageMap[p.id] = { ...p, children: [] };
    });
    
    const rootItems = [];
    pages.forEach(p => {
        if (p.parent === 0) {
            rootItems.push(pageMap[p.id]);
        } else if (pageMap[p.parent]) {
            pageMap[p.parent].children.push(pageMap[p.id]);
        }
    });

    const menuHTML = `
        <li class="menu-item"><a href="/" data-nav>Inicio</a></li>
        ${rootItems.map(item => `
            <li class="menu-item ${item.children.length ? 'has-children' : ''}">
                <a href="/page/${item.slug}" data-nav>${item.title.rendered}</a>
                ${item.children.length ? `
                    <ul class="sub-menu">
                        ${item.children.map(child => `
                            <li><a href="/page/${child.slug}" data-nav>${child.title.rendered}</a></li>
                        `).join('')}
                    </ul>
                ` : ''}
            </li>
        `).join('')}
    `;
    
    container.innerHTML = menuHTML;
    attachNavEvents(container);
}

function attachNavEvents(container) {
    container.querySelectorAll('a[data-nav]').forEach(link => {
        link.addEventListener('click', e => {
            const href = link.getAttribute('href');
            if (href.startsWith('/') || href === '') {
                e.preventDefault();
                page(href || '/');
                const toggle = document.getElementById('menu-toggle');
                if (toggle) toggle.checked = false;
            }
        });
    });
}

function renderFooterInfo() {
    const year = new Date().getFullYear();
    const siteName = state.siteInfo?.name || 'Xarop';
    if (footerInfoUI) {
        footerInfoUI.innerHTML = `
            &copy; ${year} <a href="/" data-nav>${siteName}</a> · 
            Developed in Barcelona by <a href="https://xarop.com/" target="_blank">xarop.com</a>
        `;
        attachNavEvents(footerInfoUI);
    }
}

/**
 * Components - Home
 */
function createHero(pages) {
    if (!pages || pages.length === 0) {
        return `
            <section class="hero skeleton-hero">
                <div class="container">
                    <div class="slider-container" style="background-color: var(--color-bg-dark); display: flex; align-items: center; justify-content: center;">
                        <h2 style="color: var(--color-black)">Cargando contenido de ${state.siteInfo?.name || 'Xarop'}...</h2>
                    </div>
                </div>
            </section>
        `;
    }

    return `
        <section class="hero">
            <div class="slider-container">
                ${pages.map((p, index) => {
                    const bgImage = getFeaturedImage(p);
                    const category = (p.shared_categories && p.shared_categories[0]) ? p.shared_categories[0].name : 'Destacado';
                    return `
                        <div class="slide ${index === 0 ? 'active' : ''}" 
                             data-index="${index}" 
                             style="background-image: url('${bgImage || ''}');">
                            <div class="slide-content animate-in">
                                <span class="post-category">${category}</span>
                                <h2>${p.title.rendered}</h2>
                                <div class="slide-excerpt">${p.excerpt.rendered.replace(/<[^>]*>?/gm, '').substring(0, 100)}...</div>
                                <a href="/page/${p.slug}" class="filter-btn active" data-nav>Saber más</a>
                            </div>
                        </div>
                    `;
                }).join('')}
                
                ${pages.length > 1 ? `
                    <div class="slider-dots">
                        ${pages.map((_, index) => `
                            <button class="slider-dot ${index === 0 ? 'active' : ''}" data-index="${index}"></button>
                        `).join('')}
                    </div>
                ` : ''}
            </div>
        </section>
    `;
}

function createPostsSection(categories) {
    return `
        <section id="grid" class="posts-section container">
            <div class="section-header">
                <div>
                    <span class="post-category">Nuestro Trabajo</span>
                    <h3>Publicaciones Recientes</h3>
                </div>
                <nav class="filter-nav">
                    <button class="filter-btn active" data-category="all">Todos</button>
                    ${categories.map(cat => `
                        <button class="filter-btn" data-category="${cat.id}">${cat.name}</button>
                    `).join('')}
                </nav>
            </div>
            <div id="posts-grid" class="posts-grid">
                <div class="loader-container"><div class="loader"></div></div>
            </div>
        </section>
    `;
}

function renderPostCard(post) {
    const thumbUrl = post.thumbnail ? post.thumbnail.url : 'https://via.placeholder.com/600x400?text=Sin+Imagen';
    const categoryName = post.categories && post.categories.length > 0 ? post.categories[0].name : 'Sin categoría';
    const slug = post.link.split('/').filter(Boolean).pop();
    const localLink = `/post/${slug}`;

    return `
        <article class="post-card animate-in">
            <a href="${localLink}" class="card-link" data-nav>
                <div class="post-thumb">
                    <img src="${thumbUrl}" alt="${post.title}" loading="lazy">
                </div>
                <div class="post-content">
                    <span class="post-category">${categoryName}</span>
                    <h4 class="post-title">${post.title}</h4>
                    <div class="post-excerpt">${post.excerpt}</div>
                </div>
            </a>
        </article>
    `;
}

/**
 * Logic
 */
async function updatePosts(categoryId) {
    const grid = document.getElementById('posts-grid');
    if (!grid) return;
    
    grid.innerHTML = '<div class="loader-container"><div class="loader"></div></div>';
    try {
        const posts = await fetchFilteredPosts(categoryId);
        if (posts.length === 0) {
            grid.innerHTML = '<p class="no-results">No se han encontrado resultados.</p>';
            return;
        }
        grid.innerHTML = posts.map(post => renderPostCard(post)).join('');
        attachNavEvents(grid);
    } catch (err) {
        grid.innerHTML = '<p class="no-results">Error al cargar publicaciones.</p>';
    }
}

function initSlider() {
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.slider-dot');
    if (slides.length <= 1) return;

    function goToSlide(index) {
        slides.forEach(s => s.classList.remove('active'));
        dots.forEach(d => d.classList.remove('active'));
        if (slides[index]) slides[index].classList.add('active');
        if (dots[index]) dots[index].classList.add('active');
        state.sliderIndex = index;
    }

    dots.forEach(dot => {
        dot.addEventListener('click', () => {
            const index = parseInt(dot.dataset.index);
            goToSlide(index);
            resetSliderInterval();
        });
    });

    function resetSliderInterval() {
        if (state.sliderInterval) clearInterval(state.sliderInterval);
        state.sliderInterval = setInterval(() => {
            const nextIndex = (state.sliderIndex + 1) % slides.length;
            goToSlide(nextIndex);
        }, 5000);
    }
    resetSliderInterval();
}

/**
 * Routes
 */
page('/', async () => {
    showLoader();
    updateActiveMenu('/');
    
    try {
        const [sliderPages, categories] = await Promise.all([
            fetchSliderPages(),
            fetchCategories()
        ]);

        app.innerHTML = `
            ${createHero(sliderPages)}
            ${createPostsSection(categories)}
        `;
        initSlider();
        attachNavEvents(app);
        
        const filterBtns = document.querySelectorAll('.filter-nav .filter-btn');
        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                updatePosts(btn.dataset.category);
            });
        });

        updatePosts('all');
    } catch (err) {
        app.innerHTML = '<div class="container" style="padding:100px 20px; text-align:center;"><h2>No se pudo conectar con la API central.</h2><p>Verifica la configuración en api.js</p></div>';
    }
});

page('/post/:slug', async (ctx) => {
    showLoader();
    try {
        const post = await fetchPostBySlug(ctx.params.slug);
        if (post) {
            app.innerHTML = createSingleView(post, 'post');
            window.scrollTo(0, 0);
            attachNavEvents(app);
        } else { page.redirect('/'); }
    } catch (err) { page.redirect('/'); }
});

page('/page/:slug', async (ctx) => {
    showLoader();
    try {
        const p = await fetchPageBySlug(ctx.params.slug);
        if (p) {
            app.innerHTML = createSingleView(p, 'page');
            window.scrollTo(0, 0);
            attachNavEvents(app);
        } else { page.redirect('/'); }
    } catch (err) { page.redirect('/'); }
});

page('/:slug', async (ctx) => {
    showLoader();
    try {
        const p = await fetchPageBySlug(ctx.params.slug);
        if (p) {
            app.innerHTML = createSingleView(p, 'page');
            window.scrollTo(0, 0);
            attachNavEvents(app);
        } else { page.redirect('/'); }
    } catch (err) { page.redirect('/'); }
});

function createSingleView(data, type = 'post') {
    const title = data.title.rendered;
    const content = data.content.rendered;
    const image = getFeaturedImage(data);

    return `
        <article class="single-view animate-in">
            ${image ? `<div class="single-hero" style="background-image: url('${image}')"></div>` : ''}
            <div class="container">
                <header class="single-header">
                    <h1>${title}</h1>
                </header>
                <div class="entry-content">${content}</div>
                <div class="single-footer">
                    <a href="/" class="filter-btn" data-nav>&larr; Volver</a>
                </div>
            </div>
        </article>
    `;
}

/**
 * Initialize
 */
async function init() {
    try {
        const [info, menus] = await Promise.all([
            fetchSiteInfo(),
            fetchMenus()
        ]);
        
        state.siteInfo = info;
        state.menus = menus;
        
        renderSiteInfo(info);
        
        const mainRendered = renderMenu(menus ? menus['main-menu'] : null, primaryMenuUI);
        if (!mainRendered) {
            const allPages = await fetchAllPages();
            renderFallbackMenu(allPages, primaryMenuUI);
        }

        renderFooterInfo();
    } catch (err) {
        console.error("Initialization failed:", err);
    }
    page.start();
}

document.addEventListener('DOMContentLoaded', init);
