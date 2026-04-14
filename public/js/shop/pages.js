document.addEventListener('DOMContentLoaded', () => {
    if (typeof renderWishlist === 'function') {
        renderWishlist();
    }

    // Inicializo butonat e wishlist-it
    if (window.ProductWishlist && typeof window.ProductWishlist.initWishlistButtons === 'function') {
        window.ProductWishlist.initWishlistButtons();
    }

    const isFavoritesPage = window.location.search.includes('featured=1');
    if (!isFavoritesPage || typeof getWish !== 'function') return;

    const wish = getWish() || {};
    const wishIds = Object.keys(wish).map(id => String(id));

    const grid = document.getElementById('mainProductsGrid');
    if (!grid) return;

    const cards = grid.querySelectorAll('.product-card');
    let visibleCount = 0;

    cards.forEach(card => {
        const btn = card.querySelector('.pm-wish-btn');
        if (!btn) return;

        const id = String(btn.dataset.id || '').trim();

        if (!wishIds.includes(id)) {
            card.remove();
        } else {
            visibleCount++;
        }
    });

    const headerCount = document.querySelector('.shop-header p');
    if (headerCount) {
        headerCount.textContent = `${visibleCount} produkte gjithsej`;
    }

    const pagination = document.querySelector('.pagination-wrap');
    if (pagination) {
        pagination.style.display = 'none';
    }

    if (visibleCount === 0) {
        grid.innerHTML = `
            <div class="empty-state" style="grid-column:1 / -1;">
                <i class="fa-regular fa-heart"></i>
                <h3>Nuk ke favoritet ende</h3>
                <p>Kliko zemrën te produktet për t'i shtuar këtu.</p>
                <a href="/shop" class="btn btn-outline" style="margin-top:20px;display:inline-flex;">Shiko produktet</a>
            </div>
        `;
    }
});