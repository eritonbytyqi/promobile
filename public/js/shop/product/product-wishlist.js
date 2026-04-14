(function () {
    function initWishlistButtons() {
        if (typeof getWish !== 'function') return;

        const wish = getWish() || {};

        document.querySelectorAll('.pm-wish-btn').forEach(btn => {
            const id = String(btn.dataset.id || '');
            const icon = btn.querySelector('i');
            const isFav = !!wish[id];

            if (icon) {
                icon.className = isFav ? 'fa-solid fa-heart' : 'fa-regular fa-heart';
            }

            btn.style.color = isFav ? '#ff3b30' : '';
            btn.style.background = isFav ? 'rgba(255,59,48,0.08)' : '';
            btn.style.borderColor = isFav ? 'rgba(255,59,48,0.2)' : '';
        });
    }

    function initWishlistButton() {
        const btn = document.getElementById('detailWishBtn');
        if (!btn || typeof getWish !== 'function') return;

        const wish = getWish() || {};
        const id = String(btn.dataset.id || '');
        const icon = btn.querySelector('i');
        const isFav = !!wish[id];

        if (icon) {
            icon.className = isFav ? 'fa-solid fa-heart' : 'fa-regular fa-heart';
        }

        btn.style.color = isFav ? '#ff3b30' : '';
        btn.style.background = isFav ? 'rgba(255,59,48,0.08)' : '';
        btn.style.borderColor = isFav ? 'rgba(255,59,48,0.2)' : '';
    }

    window.ProductWishlist = {
        initWishlistButtons,
        initWishlistButton
    };
})();