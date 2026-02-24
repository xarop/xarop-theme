// Simple lightbox for .gallery
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        const gallery = document.querySelector('.gallery');
        if (!gallery) return;
        const items = Array.from(gallery.querySelectorAll('a.gallery-item'));
        let currentIndex = 0;

        // Create overlay
        const overlay = document.createElement('div');
        overlay.className = 'lightbox-overlay';
        overlay.style.cssText = 'position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.9);display:flex;align-items:center;justify-content:center;z-index:9999;visibility:hidden;opacity:0;transition:opacity 0.2s;';
        const img = document.createElement('img');
        img.style.width = '100vw';
        img.style.height = 'auto';
        img.style.maxWidth = '100vw';
        img.style.maxHeight = '90vh';
        img.style.objectFit = 'contain';
        // Add navigation buttons
        // Add close (X) button
        const closeBtn = document.createElement('button');
        closeBtn.className = 'lightbox-close';
        closeBtn.innerHTML = '<span style="font-size:2.2rem;line-height:1;display:inline-block;">&times;</span>';
        closeBtn.style.cssText = 'position:absolute;top:18px;right:18px;background:none;border:none;color:#fff;font-size:2.2rem;padding:2px 8px;cursor:pointer;z-index:10002;opacity:0.85;transition:opacity 0.2s;';
        const prevBtn = document.createElement('button');
        prevBtn.className = 'lightbox-nav lightbox-prev';
        prevBtn.innerHTML = '<span style="font-size:2.5rem;line-height:1;display:inline-block;transform:rotate(-90deg);">&#x2038;</span>';
        prevBtn.style.cssText = 'position:absolute;left:16px;top:50%;transform:translateY(-50%);background:none;border:none;color:#fff;font-size:2.5rem;padding:0 6px;cursor:pointer;z-index:10001;opacity:0.85;transition:opacity 0.2s;';
        const nextBtn = document.createElement('button');
        nextBtn.className = 'lightbox-nav lightbox-next';
        nextBtn.innerHTML = '<span style="font-size:2.5rem;line-height:1;display:inline-block;transform:rotate(90deg);">&#x2038;</span>';
        nextBtn.style.cssText = 'position:absolute;right:16px;top:50%;transform:translateY(-50%);background:none;border:none;color:#fff;font-size:2.5rem;padding:0 6px;cursor:pointer;z-index:10001;opacity:0.85;transition:opacity 0.2s;';
        overlay.appendChild(prevBtn);
        overlay.appendChild(closeBtn);
        closeBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            closeLightbox();
        });
        overlay.appendChild(img);
        overlay.appendChild(nextBtn);
        // Click en la imagen para pasar a la siguiente
        img.addEventListener('click', function (e) {
            e.stopPropagation();
            showImage((currentIndex + 1) % items.length);
        });
        document.body.appendChild(overlay);

        let lastScrollY = 0;
        function showImage(index) {
            currentIndex = index;
            const item = items[currentIndex];
            img.src = item.href;
            img.alt = item.querySelector('img')?.alt || '';
        }
        function openLightbox(index) {
            showImage(index);
            overlay.style.visibility = 'visible';
            overlay.style.opacity = '1';
            lastScrollY = window.scrollY;
            document.body.style.position = 'fixed';
            document.body.style.top = `-${lastScrollY}px`;
            document.body.style.width = '100%';
        }
        function closeLightbox() {
            overlay.style.opacity = '0';
            setTimeout(() => {
                overlay.style.visibility = 'hidden';
                img.src = '';
                // Restore scroll position
                document.body.style.position = '';
                document.body.style.top = '';
                document.body.style.width = '';
                window.scrollTo(0, lastScrollY);
            }, 200);
        }
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) closeLightbox();
        });

        items.forEach((link, idx) => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                openLightbox(idx);
            });
        });

        prevBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            showImage((currentIndex - 1 + items.length) % items.length);
        });
        nextBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            showImage((currentIndex + 1) % items.length);
        });

        // Keyboard navigation
        document.addEventListener('keydown', function (e) {
            if (overlay.style.visibility === 'visible') {
                if (e.key === 'ArrowLeft') {
                    showImage((currentIndex - 1 + items.length) % items.length);
                } else if (e.key === 'ArrowRight') {
                    showImage((currentIndex + 1) % items.length);
                } else if (e.key === 'Escape') {
                    closeLightbox();
                }
            }
        });

        // Touch navigation
        let startX = 0;
        img.addEventListener('touchstart', function (e) {
            if (e.touches.length === 1) {
                startX = e.touches[0].clientX;
            }
        });
        img.addEventListener('touchend', function (e) {
            if (e.changedTouches.length === 1) {
                const endX = e.changedTouches[0].clientX;
                if (Math.abs(endX - startX) > 50) {
                    if (endX < startX) {
                        showImage((currentIndex + 1) % items.length);
                    } else {
                        showImage((currentIndex - 1 + items.length) % items.length);
                    }
                }
            }
        });
    });
})();
