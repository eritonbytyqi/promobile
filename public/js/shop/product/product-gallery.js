(function () {
    const state = window.ProductDetailState;
    const data = window.ProductData;
    const helpers = window.ProductHelpers;

    function switchMainImgDirect(src) {
        const img = document.getElementById('mainImg');
        if (!img || !src) return;

        img.classList.add('switching');

        setTimeout(() => {
            img.src = src;

            const removeSwitching = () => img.classList.remove('switching');
            img.onload = removeSwitching;
            setTimeout(removeSwitching, 400);
        }, 150);
    }

    function renderColorGallery(group) {
        const section = document.getElementById('colorGallerySection');
        const gallery = document.getElementById('colorGallery');
        if (!section || !gallery) return;

        gallery.innerHTML = '';
        const imgs = group?.images || [];

        if (!group || imgs.length === 0) {
            section.style.display = 'none';
            return;
        }

        section.style.display = '';

        imgs.forEach((src, i) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'pm-thumb' + (i === 0 ? ' active' : '');
            btn.innerHTML = `<img src="${src}" alt="foto ${i + 1}" loading="lazy">`;

            btn.addEventListener('click', () => {
                helpers.clearThumbs();
                btn.classList.add('active');
                switchMainImgDirect(src);

                state.currentIdx = data.allProductSrcs.indexOf(src);
                if (state.currentIdx < 0) state.currentIdx = 0;
            });

            gallery.appendChild(btn);
        });
    }

    function switchImage(btn, src) {
        helpers.clearThumbs();
        if (btn) btn.classList.add('active');
        switchMainImgDirect(src);

        state.currentIdx = data.allProductSrcs.indexOf(src);
        if (state.currentIdx < 0) state.currentIdx = 0;
    }

    function openLightbox() {
        const lb = document.getElementById('lightbox');
        const lbImg = document.getElementById('lbImg');
        const mainImg = document.getElementById('mainImg');
        if (!lb || !lbImg || !mainImg?.src) return;

        lbImg.src = mainImg.src;
        lb.classList.add('open');
        document.body.style.overflow = 'hidden';

        const many = data.allProductSrcs.length > 1;
        const lbPrev = document.getElementById('lbPrev');
        const lbNext = document.getElementById('lbNext');

        if (lbPrev) lbPrev.style.display = many ? 'flex' : 'none';
        if (lbNext) lbNext.style.display = many ? 'flex' : 'none';
    }

    function closeLightbox() {
        const lb = document.getElementById('lightbox');
        if (!lb) return;

        lb.classList.remove('open');
        document.body.style.overflow = '';
    }

    function lbNav(dir) {
        const lbImg = document.getElementById('lbImg');
        if (!lbImg || data.allProductSrcs.length === 0) return;

        state.currentIdx = (state.currentIdx + dir + data.allProductSrcs.length) % data.allProductSrcs.length;
        lbImg.style.opacity = '0';

        setTimeout(() => {
            lbImg.src = data.allProductSrcs[state.currentIdx];
            lbImg.style.opacity = '1';
        }, 150);
    }

    function initGallery() {
        const mainWrap = document.getElementById('mainImgWrap');
        const lb = document.getElementById('lightbox');

        mainWrap?.addEventListener('click', openLightbox);

        lb?.addEventListener('click', e => {
            if (e.target === lb) closeLightbox();
        });

        document.addEventListener('keydown', e => {
            if (!lb?.classList.contains('open')) return;

            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowRight') lbNav(1);
            if (e.key === 'ArrowLeft') lbNav(-1);
        });
    }

    window.ProductGallery = {
        switchMainImgDirect,
        renderColorGallery,
        switchImage,
        closeLightbox,
        lbNav,
        initGallery
    };

    window.switchImage = switchImage;
    window.closeLightbox = closeLightbox;
    window.lbNav = lbNav;
})();