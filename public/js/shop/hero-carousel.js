(function () {
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.hero-dot');
    const progressEl = document.getElementById('heroProgress');
    const prevBtn = document.getElementById('heroPrev');
    const nextBtn = document.getElementById('heroNext');
    const carousel = document.getElementById('heroCarousel');

    if (!slides.length) return;

    let current = 0;
    let autoTimer = null;
    let progTimer = null;
    const INTERVAL = 5000;
    const EXIT_DURATION = 600;

    function stopAllVideos() {
        slides.forEach(slide => {
            const video = slide.querySelector('.hero-bg-video');
            if (video) {
                try {
                    video.pause();
                    video.currentTime = 0;
                } catch (e) {}
            }
        });
    }

    function playActiveVideo() {
        const activeSlide = slides[current];
        if (!activeSlide) return;

        const video = activeSlide.querySelector('.hero-bg-video');
        if (video) {
            try {
                video.currentTime = 0;
                const playPromise = video.play();
                if (playPromise !== undefined) {
                    playPromise.catch(() => {});
                }
            } catch (e) {}
        }
    }

    function updateDots() {
        dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === current);
        });
    }

    function updateSlides(nextIndex) {
        const oldSlide = slides[current];
        const newSlide = slides[nextIndex];

        if (!oldSlide || !newSlide || oldSlide === newSlide) return;

        oldSlide.classList.remove('active');
        oldSlide.classList.add('exit');

        setTimeout(() => {
            oldSlide.classList.remove('exit');
        }, EXIT_DURATION);

        current = nextIndex;
        newSlide.classList.add('active');

        stopAllVideos();
        playActiveVideo();
        updateDots();
    }

    function resetProgress() {
        clearTimeout(autoTimer);
        clearTimeout(progTimer);

        if (progressEl) {
            progressEl.style.transition = 'none';
            progressEl.style.width = '0%';

            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    progressEl.style.transition = `width ${INTERVAL}ms linear`;
                    progressEl.style.width = '100%';
                });
            });
        }

        autoTimer = setTimeout(() => {
            goTo(current + 1);
        }, INTERVAL);
    }

    function goTo(n) {
        const nextIndex = (n + slides.length) % slides.length;

        if (nextIndex === current) {
            resetProgress();
            return;
        }

        updateSlides(nextIndex);
        resetProgress();
    }

    function init() {
        slides.forEach((slide, i) => {
            slide.classList.remove('active', 'exit');
            if (i === 0) slide.classList.add('active');
        });

        updateDots();
        stopAllVideos();
        playActiveVideo();

        if (slides.length > 1) {
            resetProgress();
        }
    }

    dots.forEach((dot, i) => {
        dot.addEventListener('click', () => goTo(i));
    });

    if (prevBtn) {
        prevBtn.addEventListener('click', () => goTo(current - 1));
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => goTo(current + 1));
    }

    let touchStartX = 0;

    if (carousel) {
        carousel.addEventListener('touchstart', e => {
            touchStartX = e.changedTouches[0].clientX;
        }, { passive: true });

        carousel.addEventListener('touchend', e => {
            const touchEndX = e.changedTouches[0].clientX;
            const diff = touchStartX - touchEndX;

            if (Math.abs(diff) > 50) {
                goTo(diff > 0 ? current + 1 : current - 1);
            }
        }, { passive: true });
    }

    init();
})();