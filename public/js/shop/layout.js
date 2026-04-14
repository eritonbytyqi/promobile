/* Shop Layout JS — cart, wishlist, search
   resources/js/shop/layout.js
*/

/* ── CART ── */
function addToCart(productId, btn) {
    const icon = btn?.querySelector('i');
    if (icon) icon.className = 'fa-solid fa-spinner fa-spin';

    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ product_id: productId, quantity: 1 })
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) return;
        refreshCartUI(data);
        if (icon) icon.className = 'fa-solid fa-check';
        if (btn) btn.style.background = '#34c759';
        openCartUIOnly();
        setTimeout(() => {
            if (icon) icon.className = 'fa-solid fa-plus';
            if (btn) btn.style.background = '';
        }, 1800);
    })
    .catch(err => {
        console.error('Add to cart error:', err);
        if (icon) icon.className = 'fa-solid fa-plus';
    });
}

function openCart() {
    fetch('/cart/sidebar', { headers: { 'Accept': 'application/json' } })
    .then(r => r.json())
    .then(data => { refreshCartUI(data); openCartUIOnly(); })
    .catch(err => console.error('Cart sidebar error:', err));
}

function openCartUIOnly() {
    document.getElementById('cartSidebar')?.classList.add('open');
    document.getElementById('cartOverlay')?.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeCart() {
    document.getElementById('cartSidebar')?.classList.remove('open');
    document.getElementById('cartOverlay')?.classList.remove('open');
    document.body.style.overflow = '';
}

function removeItem(cartKey) {
    fetch('/cart/remove', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ key: cartKey })
    })
    .then(r => r.json())
    .then(data => refreshCartUI(data))
    .catch(err => console.error('Remove item error:', err));
}

function updateQty(cartKey, quantity) {
    if (quantity < 1) { removeItem(cartKey); return; }
    fetch('/cart/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ key: cartKey, quantity: quantity })
    })
    .then(r => r.json())
    .then(data => refreshCartUI(data))
    .catch(err => console.error('Update qty error:', err));
}

function refreshCartUI(data) {
    document.querySelectorAll('.cart-count').forEach(el => {
        el.textContent = data.count;
        el.style.display = data.count > 0 ? 'flex' : 'none';
    });

    // Bottom nav cart badge
    const bottomCart = document.getElementById('bottomNavCartCount');
    if (bottomCart) {
        bottomCart.textContent   = data.count;
        bottomCart.style.display = data.count > 0 ? 'flex' : 'none';
    }

    const badge = document.getElementById('csCountBadge');
    if (badge) { badge.textContent = data.count; badge.style.display = data.count > 0 ? 'flex' : 'none'; }
    const totalEl = document.getElementById('csTotal');
    if (totalEl) totalEl.textContent = data.total + ' €';
    const footer = document.getElementById('csFooter');
    if (footer) footer.style.display = data.count > 0 ? '' : 'none';
    const body = document.getElementById('csBody');
    if (body && data.html) body.innerHTML = data.html;

     const shippingEl = document.querySelector('.cs-shipping');
    if (shippingEl && data.total !== undefined) {
        const freeMin  = parseFloat(document.querySelector('meta[name="shipping-free-min"]')?.content || 100);
        const cost     = parseFloat(document.querySelector('meta[name="shipping-cost"]')?.content || 2);
        const freeText = document.querySelector('meta[name="shipping-free-text"]')?.content || 'Dërgesa Falas';
        const name     = document.querySelector('meta[name="shipping-name"]')?.content || 'Kosovë';
        const total    = parseFloat(data.total.replace(',', '.'));

    const country  = document.querySelector('meta[name="shipping-flag"]')?.content || 'kosovo';
const flagHtml = getFlagHtml(country);

if (total >= freeMin) {
    shippingEl.innerHTML = `${flagHtml} ${freeText} — ${name}`;
} else {
    const diff = (freeMin - total).toFixed(2);
    shippingEl.innerHTML = `${flagHtml} Dërgesa ${name} ${cost.toFixed(2)} € — shto ${diff} € për falas`;
}
    }
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeCart(); });
function getFlagHtml(country) {
    const flags = {
        'kosovo':    '<img src="/images/flags/xk.svg" style="width:16px;height:12px;border-radius:2px;vertical-align:middle;">',
        'albania':   '<img src="/images/flags/al.svg" style="width:16px;height:12px;border-radius:2px;vertical-align:middle;">',
        'macedonia': '<img src="/images/flags/mk.svg" style="width:16px;height:12px;border-radius:2px;vertical-align:middle;">',
        'serbia':    '<img src="/images/flags/rs.svg" style="width:16px;height:12px;border-radius:2px;vertical-align:middle;">',
    };
    return flags[country] || '';
}
/* ── WISHLIST ── */
const WISH_KEY = 'shopzone_wishlist';

function getWish() {
    try { return JSON.parse(localStorage.getItem(WISH_KEY)) || {}; }
    catch { return {}; }
}

function saveWish(data) {
    localStorage.setItem(WISH_KEY, JSON.stringify(data));
}
function toggleWish(btn) {
    const id   = btn.dataset.id;
    const wish = getWish();
    const icon = btn.querySelector('i');

    if (wish[id]) {
        delete wish[id];
        btn.classList.remove('saved');
        icon.className = 'fa-regular fa-heart';
        btn.style.color = '#ccc';
        btn.style.background = '#f5f5f7';
        btn.style.borderColor = 'rgba(0,0,0,0.1)';
    } else {
        wish[id] = {
            id:    id,
            name:  btn.dataset.name,
            price: btn.dataset.price,
            img:   btn.dataset.img,
            url:   btn.dataset.url,
            cat:   btn.dataset.cat,
        };
        btn.classList.add('saved');
        icon.className = 'fa-solid fa-heart';
        btn.style.color = '#ff3b30';
        btn.style.background = 'rgba(255,59,48,0.08)';
        btn.style.borderColor = 'rgba(255,59,48,0.2)';
        btn.classList.remove('pop');
        void btn.offsetWidth;
        btn.classList.add('pop');
        btn.addEventListener('animationend', () => btn.classList.remove('pop'), { once: true });
    }

    saveWish(wish);
    updateNavWishCount();
    renderWishlist();
}
function renderWishlist() {
    const wish    = getWish();
    const items   = Object.values(wish);
    const section = document.getElementById('wishlistSection');
    const grid    = document.getElementById('wishlistGrid');

    if (!section || !grid) return;

    if (!items.length) {
        section.style.display = 'none';
        return;
    }

    section.style.display = 'block';
    grid.innerHTML = items.map(function(p) {
        var imgHtml = p.img
            ? '<img src="' + p.img + '" alt="' + p.name + '" loading="lazy">'
            : '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#ccc;font-size:48px;"><i class="fa-solid fa-box"></i></div>';

return '<a href="' + p.url + '" class="product-card" style="text-decoration:none;">'
    + '<div class="product-img" style="aspect-ratio:1;overflow:hidden;border-radius:var(--radius-2xl);background:var(--surface);margin-bottom:16px;position:relative;">'
    + imgHtml
    + '<button class="pm-wish-btn saved"'
    + ' data-id="' + p.id + '"'
    + ' data-name="' + p.name + '"'
    + ' data-price="' + p.price + '"'
    + ' data-img="' + p.img + '"'
    + ' data-url="' + p.url + '"'
    + ' data-cat="' + p.cat + '"'
    + ' onclick="event.preventDefault(); toggleWish(this)">'
    + '<i class="fa-solid fa-heart"></i>'
    + '</button>'
    + '</div>'
    + '<div class="product-card-body">'
    + '<div class="product-card-meta">'
    + '<div>'
    + '<div class="product-card-name">' + p.name + '</div>'
    + '<div class="product-card-sub">' + p.cat + '</div>'
    + '</div>'
    + '<div class="product-card-price">' + p.price + ' \u20AC</div>'
    + '</div>'
    + '</div>'
    + '</a>';
    }).join('');
}
document.addEventListener('DOMContentLoaded', () => {
    const wish = getWish();
    document.querySelectorAll('.pm-wish-btn[data-id]').forEach(btn => {
        if (wish[btn.dataset.id]) {
            btn.classList.add('saved');
            btn.querySelector('i').className = 'fa-solid fa-heart';
        } else {
            btn.querySelector('i').className = 'fa-regular fa-heart';
        }
    });
    updateNavWishCount();
    renderWishlist();
});

function updateNavWishCount() {
    const wish  = getWish();
    const count = Object.keys(wish).length;

    const badge      = document.getElementById('navWishCount');
    const bottomBadge = document.getElementById('bottomNavWishCount');

    if (badge) {
        badge.textContent   = count;
        badge.style.display = count > 0 ? 'inline-flex' : 'none';
    }
    if (bottomBadge) {
        bottomBadge.textContent   = count;
        bottomBadge.style.display = count > 0 ? 'flex' : 'none';
    }
}
/* ── LIVE SEARCH ── */
(function() {
    const input    = document.getElementById('searchInput');
    const dropdown = document.getElementById('searchDropdown');
    if (!input || !dropdown) return;

    let timer = null;

    input.addEventListener('input', function() {
        clearTimeout(timer);
        const q = this.value.trim();

        if (q.length < 1) {
            dropdown.style.display = 'none';
            return;
        }

        timer = setTimeout(function() {
            fetch('/shop/search-live?q=' + encodeURIComponent(q))
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (!data.length) {
                    dropdown.innerHTML = '<div class="search-result-empty">Nuk u gjet asgjë</div>';
                    dropdown.style.display = 'block';
                    return;
                }

                dropdown.innerHTML = data.map(function(p) {
                    return '<a href="/shop/' + p.id + '" class="search-result-item">'
                        + (p.img ? '<img src="' + p.img + '" alt="' + p.name + '">' : '<div style="width:36px;height:36px;border-radius:8px;background:#f3f3f5;flex-shrink:0;"></div>')
                        + '<span class="search-result-name">' + p.name + '</span>'
                        + '<span class="search-result-price">' + p.price + ' €</span>'
                        + '</a>';
                }).join('');

                dropdown.style.display = 'block';
            })
            .catch(function() { dropdown.style.display = 'none'; });
        }, 220);
    });

    document.addEventListener('click', function(e) {
        if (!input.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    input.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') dropdown.style.display = 'none';
    });
})();
function toggleProfileMenu() {
    const menu = document.getElementById('profileMenu');
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}

document.addEventListener('click', function(e) {
    const wrap = document.getElementById('profileDropdownWrap');
    if (wrap && !wrap.contains(e.target)) {
        const menu = document.getElementById('profileMenu');
        if (menu) menu.style.display = 'none';
    }
});