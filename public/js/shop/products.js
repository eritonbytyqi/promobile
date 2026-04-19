function addToCart(id, btn, variantId) {
    variantId = variantId || null;
    var icon = btn.querySelector('i');
    if (icon) icon.className = 'fa-solid fa-spinner fa-spin';

    var payload = { product_id: id, quantity: 1 };
    if (variantId) payload.variant_id = variantId;

    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (!data.success) return;
        refreshCartUI(data);
        if (icon) icon.className = 'fa-solid fa-check';
        btn.style.background = '#34c759';
        openCartUIOnly();
        setTimeout(function() {
            if (icon) icon.className = 'fa-solid fa-plus';
            btn.style.background = '';
        }, 1800);
    })
    .catch(function(err) {
        console.error('Cart error:', err);
        if (icon) icon.className = 'fa-solid fa-plus';
    });
}

function toggleDrop(btn) {
    const wrap    = btn.closest('.brand-dd-wrap');
    const brandId = wrap.dataset.brandId;
    const subs    = (window.brandSubcategories || {})[brandId] || [];

    if (subs.length === 0) {
        window.location.href = '/shop?category=' + window.currentCategory + '&brand=' + brandId;
        return;
    }

    const isOpen = wrap.classList.contains('open');

    document.querySelectorAll('.brand-dd-wrap').forEach(w => w.classList.remove('open'));

    if (!isOpen) {
        wrap.classList.add('open');
    }
}
document.addEventListener('DOMContentLoaded', function() {
    renderWishlist();

    const wish  = getWish();
    const items = Object.values(wish);
    const isFavoritetPage = window.location.search.includes('featured=1');

    if (isFavoritetPage) {
        var mainGrid = document.getElementById('mainProductsGrid');
        if (mainGrid) mainGrid.remove();
        var pagination = document.querySelector('.pagination-wrap');
        if (pagination) pagination.remove();
        var shopHeader = document.querySelector('.shop-header p');
        if (shopHeader) shopHeader.remove();
        var shopWrap = document.querySelector('.shop-wrap');
        if (shopWrap) {
            shopWrap.style.paddingBottom = '0';
            shopWrap.style.marginBottom = '0';
        }
        if (items.length === 0) {
            var section = document.getElementById('wishlistSection');
            if (section) {
                section.style.display = 'block';
                section.innerHTML = '<div style="text-align:center;padding:40px 20px;color:#6e6e73;">'
                    + '<i class="fa-regular fa-heart" style="font-size:48px;display:block;margin-bottom:12px;opacity:0.3;"></i>'
                    + '<h3 style="font-size:18px;font-weight:700;margin-bottom:6px;color:#1a1c1d;">Nuk ke favoritet ende</h3>'
                    + '<p style="font-size:14px;">Kliko zemrën te produktet për t\'i shtuar këtu.</p>'
                    + '</div>';
            }
        }
    } else {
        var empty = document.getElementById('emptyState');
        if (empty && items.length > 0) {
            empty.style.display = 'none';
        }
    }

    // ── BRAND DROPDOWN ──
    const brandSubcategories = window.brandSubcategories || {};
    const currentCategory    = window.currentCategory    || '';
    const currentBrand       = window.currentBrand       || '';
    const currentSubcat      = window.currentSubcat      || '';

 document.querySelectorAll('.brand-dd-wrap').forEach(function(wrap) {
    const brandId = wrap.dataset.brandId;
    const subs    = brandSubcategories[brandId] || [];
        console.log('Brand ID:', brandId, '→ Subs:', subs); // ← SHTO KËTË

    const menu    = wrap.querySelector('.brand-dd-menu');
    if (!menu) return;

    subs.forEach(function(sub) {
        const a = document.createElement('a');
        a.href = '/shop?category=' + currentCategory + '&brand=' + brandId + '&subcategory=' + encodeURIComponent(sub);
        a.className = 'brand-dd-item' + (currentBrand == brandId && currentSubcat === sub ? ' active' : '');
        a.textContent = sub;
        menu.appendChild(a);
    });

    if (currentBrand == brandId && subs.length > 0) {
        wrap.classList.add('open');
    }
});

document.addEventListener('click', function(e) {
    if (!e.target.closest('.brand-dd-wrap')) {
        document.querySelectorAll('.brand-dd-wrap').forEach(w => w.classList.remove('open'));
    }
});
window.addToCart = function (id, btn, variantId) {
    variantId = variantId || null;
    var icon = btn.querySelector('i');
    if (icon) icon.className = 'fa-solid fa-spinner fa-spin';

    var payload = { product_id: id, quantity: 1 };
    if (variantId) payload.variant_id = variantId;

    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (!data.success) return;
        refreshCartUI(data);
        if (icon) icon.className = 'fa-solid fa-check';
        btn.style.background = '#34c759';
        openCartUIOnly();
        setTimeout(function() {
            if (icon) icon.className = 'fa-solid fa-plus';
            btn.style.background = '';
        }, 1800);
    })
    .catch(function(err) {
        console.error('Cart error:', err);
        if (icon) icon.className = 'fa-solid fa-plus';
    });
};

document.addEventListener('DOMContentLoaded', function() {
    renderWishlist();

    const wish  = getWish();
    const items = Object.values(wish);
    const isFavoritetPage = window.location.search.includes('featured=1');

    if (isFavoritetPage) {
        var mainGrid = document.getElementById('mainProductsGrid');
        if (mainGrid) mainGrid.remove();

        var pagination = document.querySelector('.pagination-wrap');
        if (pagination) pagination.remove();

        var shopHeader = document.querySelector('.shop-header p');
        if (shopHeader) shopHeader.remove();

        var shopWrap = document.querySelector('.shop-wrap');
        if (shopWrap) {
            shopWrap.style.paddingBottom = '0';
            shopWrap.style.marginBottom = '0';
        }

        if (items.length === 0) {
            var section = document.getElementById('wishlistSection');
            if (section) {
                section.style.display = 'block';
                section.innerHTML = '<div style="text-align:center;padding:40px 20px;color:#6e6e73;">'
                    + '<i class="fa-regular fa-heart" style="font-size:48px;display:block;margin-bottom:12px;opacity:0.3;"></i>'
                    + '<h3 style="font-size:18px;font-weight:700;margin-bottom:6px;color:#1a1c1d;">Nuk ke favoritet ende</h3>'
                    + '<p style="font-size:14px;">Kliko zemrën te produktet për t\'i shtuar këtu.</p>'
                    + '</div>';
            }
        }
    } else {
        var empty = document.getElementById('emptyState');
        if (empty && items.length > 0) {
            empty.style.display = 'none';
        }
    }
});
function syncMobileCheck(cb) {
    // Sync me checkbox-in e tabelës
    const tableCheck = document.querySelector(
        `tbody .row-check[value="${cb.value}"]:not(.mobile-check)`
    );
    if (tableCheck) {
        tableCheck.checked = cb.checked;
        tableCheck.dispatchEvent(new Event('change'));
    }
}
});