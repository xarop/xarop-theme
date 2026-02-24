/**
 * Main JavaScript File
 * 
 * Handles slider functionality and category-based post filtering
 * 
 * @package Xarop_Theme
 * @since 1.0.0
 */

(function () {
  'use strict';

  /**
   * Home Slider Functionality
   */
  function initSlider() {
    const sliderContainer = document.querySelector('.slider-container');
    if (!sliderContainer) return;

    const slides = sliderContainer.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.slider-dot');

    if (slides.length <= 1) return;

    let currentSlide = 0;
    const totalSlides = slides.length;

    // Update slider position
    function updateSlider(index) {
      currentSlide = index;
      sliderContainer.setAttribute('data-current-slide', currentSlide);
      slides.forEach((slide, i) => {
        slide.style.display = i === currentSlide ? 'block' : 'none';
      });
      dots.forEach((dot, i) => {
        dot.classList.toggle('active', i === currentSlide);
      });
    }

    // Initialize: show only the first slide
    slides.forEach((slide, i) => {
      slide.style.display = i === 0 ? 'block' : 'none';
    });
    dots.forEach((dot, i) => {
      dot.classList.toggle('active', i === 0);
    });

    // Dot click handlers
    dots.forEach((dot, index) => {
      dot.addEventListener('click', () => {
        updateSlider(index);
      });
    });

    // Auto-advance slider every 5 seconds
    setInterval(() => {
      const nextSlide = (currentSlide + 1) % totalSlides;
      updateSlider(nextSlide);
    }, 5000);

    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowLeft') {
        const prevSlide = (currentSlide - 1 + totalSlides) % totalSlides;
        updateSlider(prevSlide);
      } else if (e.key === 'ArrowRight') {
        const nextSlide = (currentSlide + 1) % totalSlides;
        updateSlider(nextSlide);
      }
    });

    // Touch navigation for mobile
    let touchStartX = 0;
    let touchEndX = 0;
    sliderContainer.addEventListener('touchstart', function (e) {
      if (e.touches.length === 1) {
        touchStartX = e.touches[0].clientX;
      }
    });
    sliderContainer.addEventListener('touchend', function (e) {
      if (e.changedTouches.length === 1) {
        touchEndX = e.changedTouches[0].clientX;
        if (Math.abs(touchEndX - touchStartX) > 50) {
          if (touchEndX < touchStartX) {
            // Swipe left, next slide
            const nextSlide = (currentSlide + 1) % totalSlides;
            updateSlider(nextSlide);
          } else {
            // Swipe right, previous slide
            const prevSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            updateSlider(prevSlide);
          }
        }
      }
    });
  }

  /**
   * posts Grid Filtering
   */
  function initPostsGrid() {
    const gridContainer = document.getElementById('grid');
    if (!gridContainer) return;

    const filterButtons = document.querySelectorAll('.filter-btn');
    let currentCategory = 'all';

    // Load posts (with optional page)
    function loadPosts(categoryId, page = 1) {
      gridContainer.classList.add('loading');
      const formData = new FormData();
      formData.append('action', 'filter_posts_html');
      formData.append('category', categoryId);
      formData.append('page', page);
      // Puedes agregar per_page si lo necesitas desde PHP
      fetch(xaropData.ajaxUrl, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
      })
        .then(response => response.json())
        .then(data => {
          if (data.success && data.data && data.data.html) {
            gridContainer.innerHTML = data.data.html;
            attachPaginationHandlers();
          } else {
            gridContainer.innerHTML = '<div class="no-results">' +
              '<p>No posts found in this category.</p>' +
              '</div>';
          }
          gridContainer.classList.remove('loading');
        })
        .catch(error => {
          console.error('Error loading posts:', error);
          gridContainer.innerHTML = '<div class="no-results">' +
            '<p>Error loading posts. Please try again.</p>' +
            '</div>';
          gridContainer.classList.remove('loading');
        });
    }

    // Adjuntar eventos a los enlaces de paginación
    function attachPaginationHandlers() {
      const paginationLinks = gridContainer.querySelectorAll('.pagination a');
      paginationLinks.forEach(link => {
        link.addEventListener('click', function (e) {
          e.preventDefault();
          const url = new URL(this.href);
          let page = url.searchParams.get('paged') || url.searchParams.get('page') || 1;
          page = parseInt(page, 10);
          loadPosts(currentCategory, page);
        });
      });
    }

    // Remove old renderposts function

    // (removed: old renderposts function)
    // (removed: orphaned escapeHtml function)

    // Filter button click handlers
    filterButtons.forEach(button => {
      button.addEventListener('click', function () {
        const categoryId = this.getAttribute('data-category');
        filterButtons.forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');
        currentCategory = categoryId;
        loadPosts(categoryId, 1);
      });
    });
    loadPosts('all', 1);
  }

  /**
   * Close mobile menu when clicking overlay
   */
  function initMenuOverlay() {
    const menuOverlay = document.querySelector('.menu-overlay');
    const menuToggle = document.getElementById('menu-toggle');

    if (menuOverlay && menuToggle) {
      menuOverlay.addEventListener('click', () => {
        menuToggle.checked = false;
      });
    }
  }

  /**
   * Close mobile menu when clicking a menu link
   */
  function initMenuLinks() {
    const menuToggle = document.getElementById('menu-toggle');
    const menuLinks = document.querySelectorAll('.main-navigation a');

    if (menuToggle && menuLinks.length > 0) {
      menuLinks.forEach(link => {
        link.addEventListener('click', () => {
          menuToggle.checked = false;
        });
      });
    }
  }

  /**
   * Initialize all functionality when DOM is ready
   */
  function init() {
    initSlider();
    initPostsGrid();
    initMenuOverlay();
    initMenuLinks();
  }

  // Run on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
