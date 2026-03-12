document.addEventListener('DOMContentLoaded', function() {
    const carousels = document.querySelectorAll('.carousel-container');

    carousels.forEach(carousel => {
        const track = carousel.querySelector('.carousel-track');
        // Look for buttons globally scoped to the section header nearby
        const section = carousel.closest('section');
        const nextBtn = section ? section.querySelector('.next-btn') : null;
        const prevBtn = section ? section.querySelector('.prev-btn') : null;
        
        if (!track) return;

        let index = 0;
        const isBanner = carousel.classList.contains('banner-carousel-container');
        
        function updateCarousel() {
            const firstItem = track.querySelector('.carousel-item-custom, .banner-slide');
            if (!firstItem) return;
            
            // Get precise width
            const rect = firstItem.getBoundingClientRect();
            const itemWidth = rect.width;
            const containerWidth = carousel.offsetWidth;
            const trackWidth = track.scrollWidth;
            
            // Calculate max index
            const itemsCount = track.children.length;
            const visibleCount = Math.round(containerWidth / itemWidth);
            const maxIndex = isBanner ? itemsCount - 1 : Math.max(0, itemsCount - visibleCount);
            
            if (index > maxIndex) index = maxIndex;
            if (index < 0) index = 0;

            track.style.transform = `translateX(-${index * itemWidth}px)`;
            
            if (prevBtn) prevBtn.disabled = index === 0;
            if (nextBtn) nextBtn.disabled = index >= maxIndex;
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                const itemsCount = track.children.length;
                const containerWidth = carousel.offsetWidth;
                const itemWidth = track.querySelector('.carousel-item-custom, .banner-slide').offsetWidth;
                const visibleCount = Math.round(containerWidth / itemWidth);
                const maxIndex = isBanner ? itemsCount - 1 : Math.max(0, itemsCount - visibleCount);

                if (index < maxIndex) {
                    index++;
                    updateCarousel();
                }
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                if (index > 0) {
                    index--;
                    updateCarousel();
                }
            });
        }

        // Auto slide for banner
        if (isBanner) {
            setInterval(() => {
                const itemsCount = track.children.length;
                if (index < itemsCount - 1) {
                    index++;
                } else {
                    index = 0;
                }
                updateCarousel();
            }, 5000);
        }

        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(updateCarousel, 100);
        });

        // Initialize with a slight delay to ensure layout is ready
        setTimeout(updateCarousel, 300);
    });
});
