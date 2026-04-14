document.addEventListener('DOMContentLoaded', () => {
    renderWishlist();
});function renderWishlist() {
    const wish   = typeof getWish === 'function' ? (getWish() || {}) : {};
    const items  = Object.values(wish);
    const section = document.getElementById('wishlistSection');
    const grid    = document.getElementById('wishlistGrid');

    if (!section || !grid) return;

    if (items.length === 0) {
        section.style.display = 'none';
        return;
    }

    section.style.display = 'block';
    grid.innerHTML = '';

    items.forEach(item => {
        const card = document.createElement('a');
        card.href      = item.url || '#';
        card.className = 'pm-product-card';
        card.innerHTML = `
            <div class="pm-product-img">
                ${item.img
                    ? `<img src="${item.img}" alt="${item.name}" style="width:100%;height:100%;object-fit:cover;">`
                    : `<div class="pm-product-img-ph"><i class="fa-solid fa-box"></i></div>`
                }
                <button class="pm-wish-btn saved"
                    data-id="${item.id}"
                    data-name="${item.name}"
                    data-price="${item.price}"
                    data-img="${item.img || ''}"
                    data-url="${item.url || ''}"
                    data-cat="${item.cat || ''}"
                    onclick="event.preventDefault(); toggleWish(this)"
                    type="button">
                    <i class="fa-solid fa-heart"></i>
                </button>
            </div>
            <div class="pm-product-info">
                <div class="pm-product-cat">${item.cat || ''}</div>
                <div class="pm-product-name">${item.name}</div>
                <div class="pm-product-footer">
                    <span class="pm-product-price">${item.price} €</span>
                </div>
            </div>
        `;
        grid.appendChild(card);
    });
}