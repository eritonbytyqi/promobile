    document.addEventListener('DOMContentLoaded', () => {
    renderWishlist();

    const wish = getWish();
    const items = Object.values(wish);

    const empty = document.getElementById('emptyState');
    if (empty && items.length > 0) {
        empty.style.display = 'none';
    }
});