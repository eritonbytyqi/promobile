const {
    colorGroups,
    productId: PRODUCT_ID,
    basePrice: BASE_PRICE,
    baseOld: BASE_OLD,
    baseStock: BASE_STOCK,
    productImages,
    colorImages
} = window.productDetailData || {
    colorGroups: [],
    productId: null,
    basePrice: 0,
    baseOld: null,
    baseStock: 0,
    productImages: [],
    colorImages: []
};

let selColorName = null;
let selVariantId = null;
const allProductSrcs = [...productImages, ...colorImages];
let currentIdx = 0;

function getGroup(colorName) {
    if (!colorName) return null;
    return colorGroups.find(g => g.color_name === colorName) || null;
}

function getStorage(group, variantId) {
    if (!group || !variantId) return null;
    return group.storages.find(s => s.id == variantId) || null;
}

window.selectColor = function(el, name) {
    if (selColorName === name && el.classList.contains('active')) {
        selColorName = null;
        selVariantId = null;
        el.classList.remove('active');
        renderStorages(null);
        renderColorGallery(null);
        refreshUI();
        return;
    }

    selColorName = name;
    selVariantId = null;

    const orderBtn = document.getElementById('orderBtn');
    if (orderBtn) orderBtn.classList.remove('success');

    const orderBtnText = document.getElementById('orderBtnText');
    if (orderBtnText) orderBtnText.textContent = 'Shto në shportë';

    document.querySelectorAll('.pm-color').forEach(c => c.classList.remove('active'));
    el.classList.add('active');

    const lbl = document.getElementById('colorLabel');
    if (lbl) lbl.textContent = name;

    const group = getGroup(name);
    const firstAvail = group?.storages?.find(s => s.stock > 0) || group?.storages?.[0] || null;
    if (firstAvail) selVariantId = firstAvail.id;

    renderStorages(group);
    renderColorGallery(group);

    document.querySelectorAll('#productThumbs .pm-thumb').forEach(b => b.classList.remove('active'));

    if (group && group.images.length > 0) {
        switchMainImgDirect(group.images[0]);
    }

    refreshUI();
};
function selectStorage(el) {
    const variantId = parseInt(el.dataset.variantId, 10);
    if (!variantId) return;

    selVariantId = variantId;

    const orderBtn = document.getElementById('orderBtn');
    if (orderBtn) orderBtn.classList.remove('success');

    const orderBtnText = document.getElementById('orderBtnText');
    if (orderBtnText) orderBtnText.textContent = 'Shto në shportë';

    document.querySelectorAll('.pm-storage').forEach(s => s.classList.remove('active'));
    el.classList.add('active');

    const group = getGroup(selColorName);
    const storage = group?.storages?.find(s => s.id === variantId);
    const lbl = document.getElementById('storageLabel');
    if (lbl && storage) lbl.textContent = storage.storage || '';

    refreshUI();
}

function renderStorages(group) {
    const grid = document.getElementById('storageGrid');
    const section = document.getElementById('storageSection');
    if (!grid || !section) return;

    const storages = (group?.storages || []).filter(s => s.storage && s.storage.trim() !== '');

    if (!group || !storages.length) {
        section.style.display = 'none';
        grid.innerHTML = '';
        return;
    }

    section.style.display = '';
    grid.innerHTML = '';

    storages.forEach((sv, i) => {
        const extra = sv.extra_price ?? 0;
        const isOut = sv.stock <= 0;
        const isActive = sv.id === selVariantId || (!selVariantId && i === 0);

        const btn = document.createElement('div');
        btn.className = 'pm-storage' + (isActive ? ' active' : '') + (isOut ? ' out' : '');
        btn.dataset.storage = sv.storage;
        btn.dataset.variantId = sv.id;

        btn.innerHTML = `
            <span class="pm-storage-name">${sv.storage}</span>
            <span class="pm-storage-price">
                ${isOut ? 'Jashtë stoku' : (extra > 0 ? '+' + extra.toFixed(2) + ' €' : 'Bazë')}
            </span>
        `;

        if (!isOut) btn.addEventListener('click', () => selectStorage(btn));

        grid.appendChild(btn);
    });
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
            document.querySelectorAll('#colorGallery .pm-thumb').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('#productThumbs .pm-thumb').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            switchMainImgDirect(src);
            currentIdx = allProductSrcs.indexOf(src);
            if (currentIdx < 0) currentIdx = 0;
        });

        gallery.appendChild(btn);
    });
}

window.switchImage = function(btn, src) {
    document.querySelectorAll('#colorGallery .pm-thumb').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('#productThumbs .pm-thumb').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    switchMainImgDirect(src);
    currentIdx = allProductSrcs.indexOf(src);
    if (currentIdx < 0) currentIdx = 0;
};

function switchMainImgDirect(src) {
    const img = document.getElementById('mainImg');
    if (!img) return;

    img.classList.add('switching');

    setTimeout(() => {
        img.src = src;
        img.onload = () => img.classList.remove('switching');
        setTimeout(() => img.classList.remove('switching'), 400);
    }, 150);
}

function refreshUI() {
       const stockWrap = document.getElementById('stockWrap');
    if (stockWrap) {
        stockWrap.style.display = (selColorName || colorGroups.length === 0) ? '' : 'none';
    }

    const group = getGroup(selColorName);
    const storage = getStorage(group, selVariantId);
    const basePrice = group?.storages?.[0]?.base_price ?? BASE_PRICE;
    const extraPrice = storage?.extra_price ?? 0;

    const finalPrice = storage
        ? (storage.sale_price ?? (basePrice + extraPrice))
        : BASE_PRICE;

    const stock = storage ? storage.stock : BASE_STOCK;

    const priceEl = document.getElementById('priceMain');
    if (priceEl) {
        priceEl.style.opacity = '0';

        setTimeout(() => {
            const hasVariants = colorGroups.length > 0;
            if (storage) {
                priceEl.innerHTML = finalPrice.toFixed(2) + ' €';
            } else if (hasVariants) {
                priceEl.innerHTML = '<span style="font-size:16px;font-weight:500;color:#6e6e73;margin-right:4px;">nga</span>' + finalPrice.toFixed(2) + ' €';
            } else {
                priceEl.innerHTML = finalPrice.toFixed(2) + ' €';
            }
            priceEl.style.opacity = '1';
        }, 150);
    }

    const oldEl = document.getElementById('priceOld');
    const badgeEl = document.getElementById('priceBadge');

    if (BASE_OLD && oldEl && badgeEl) {
        const pct = Math.round(((BASE_OLD - finalPrice) / BASE_OLD) * 100);
        oldEl.textContent = BASE_OLD.toFixed(2) + ' €';
        oldEl.style.display = '';
        badgeEl.textContent = '-' + pct + '%';
        badgeEl.style.display = '';
    } else if (oldEl && badgeEl) {
        oldEl.style.display = 'none';
        badgeEl.style.display = 'none';
    }

    const dot = document.getElementById('stockDot');
    const txt = document.getElementById('stockText');

    if (dot && txt) {
        dot.className = 'pm-stock-dot';
        if (stock > 10) {
            dot.classList.add('in');
            txt.textContent = 'Në stok — ' + stock + ' të mbetur';
        } else if (stock > 0) {
            dot.classList.add('low');
            txt.textContent = 'Vetëm ' + stock + ' të mbetur!';
        } else {
            dot.classList.add('out');
            txt.textContent = 'Jashtë stoku';
        }
    }

const btn = document.getElementById('orderBtn');
const btnTxt = document.getElementById('orderBtnText');

if (btn && btnTxt) {
   const hasVariants = colorGroups.length > 0;

if (stock <= 0) {
    btn.classList.add('disabled');
    btn.disabled = true;
    btnTxt.textContent = 'Jashtë Stoku';
} else if (hasVariants && !selColorName) {
    btn.classList.add('disabled');
    btn.disabled = true;
    btnTxt.textContent = 'Zgjedh ngjyrën';
} else if (hasVariants && selColorName && !selVariantId) {
    btn.classList.add('disabled');
    btn.disabled = true;
    btnTxt.textContent = 'Zgjedh storage-in';
} else {
    btn.classList.remove('disabled');
    btn.disabled = false;
    btnTxt.textContent = 'Shto në shportë';
}
}
    const toast = document.getElementById('variantToast');
    const toastTxt = document.getElementById('variantToastText');
    if (!toast || !toastTxt) return;

    if (!group) {
        toast.classList.remove('ok');
        toastTxt.innerHTML = 'Zgjedh ngjyrën për të parë fotot, storage dhe çmimin e variantit';
        return;
    }

    if (storage) {
        toast.classList.add('ok');
        const parts = [];
        if (group?.color_name) parts.push(group.color_name);
        if (storage.storage) parts.push(storage.storage);
        toastTxt.innerHTML = '✓ ' + parts.join(' · ') + ' — <strong>' + finalPrice.toFixed(2) + ' €</strong>';
    } else {
        toast.classList.remove('ok');
        toastTxt.innerHTML = '✓ ' + (group?.color_name || 'Ngjyra') + ' — zgjidh storage';
    }
}

const lb = document.getElementById('lightbox');
const lbImg = document.getElementById('lbImg');

document.getElementById('mainImgWrap')?.addEventListener('click', () => {
    const src = document.getElementById('mainImg')?.src;
    if (!src || !lb || !lbImg) return;

    lbImg.src = src;
    lb.classList.add('open');
    document.body.style.overflow = 'hidden';

    const many = allProductSrcs.length > 1;
    const lbPrev = document.getElementById('lbPrev');
    const lbNext = document.getElementById('lbNext');

    if (lbPrev) lbPrev.style.display = many ? 'flex' : 'none';
    if (lbNext) lbNext.style.display = many ? 'flex' : 'none';
});

window.closeLightbox = function() {
    if (!lb) return;
    lb.classList.remove('open');
    document.body.style.overflow = '';
};


window.lbNav = function(dir) {
    if (!lbImg || allProductSrcs.length === 0) return;

    currentIdx = (currentIdx + dir + allProductSrcs.length) % allProductSrcs.length;
    lbImg.style.opacity = '0';

    setTimeout(() => {
        lbImg.src = allProductSrcs[currentIdx];
        lbImg.style.opacity = '1';
    }, 150);
};  
lb?.addEventListener('click', e => {
    if (e.target === lb) closeLightbox();
});

document.addEventListener('keydown', e => {
    if (!lb?.classList.contains('open')) return;
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowRight') lbNav(1);
    if (e.key === 'ArrowLeft') lbNav(-1);
});

window.addCurrentProductToCart = function(button) {
    if (!button || button.disabled) return;

    const group = getGroup(selColorName);
    const storage = getStorage(group, selVariantId);
    const payload = { product_id: PRODUCT_ID, quantity: 1 };

    if (storage) payload.variant_id = storage.id;

    // ✅ Këto 3 rreshta shto i
    if (group && group.images && group.images.length > 0) {
        payload.image = group.images[0];
    }
    if (selColorName) payload.color = selColorName;
    if (storage?.storage) payload.storage = storage.storage;

    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('cartPopup')?.classList.add('open');
            document.body.style.overflow = 'hidden';
            button.classList.add('success');

            const orderBtnText = document.getElementById('orderBtnText');
            if (orderBtnText) orderBtnText.textContent = 'U shtua ✓';

            if (typeof openCart === 'function') openCart();

            if (data.cart_html && document.getElementById('csBody')) {
                document.getElementById('csBody').innerHTML = data.cart_html;
            }

            if (typeof data.cart_count !== 'undefined') {
                const badge = document.getElementById('csCountBadge');
                if (badge) {
                    badge.textContent = data.cart_count;
                    badge.style.display = data.cart_count > 0 ? '' : 'none';
                }
            }

            if (typeof data.cart_total !== 'undefined') {
                const total = document.getElementById('csTotal');
                if (total) total.textContent = data.cart_total + ' €';
            }
        }
    })
    .catch(console.error);
};


window.closeCartPopup = function() {
    document.getElementById('cartPopup')?.classList.remove('open');
    document.body.style.overflow = '';
};

document.addEventListener('DOMContentLoaded', () => {
    const priceMain = document.getElementById('priceMain');
    if (priceMain) priceMain.style.transition = 'opacity 0.15s';

    refreshUI();

    document.getElementById('cartPopup')?.addEventListener('click', function(e) {
        if (e.target === this) closeCartPopup();
    });

    const btn = document.getElementById('detailWishBtn');
    if (!btn) return;

    const wish = getWish();
    const id = btn.dataset.id;

    if (wish[id]) {
        btn.querySelector('i').className = 'fa-solid fa-heart';
        btn.style.color = '#ff3b30';
        btn.style.background = 'rgba(255,59,48,0.08)';
        btn.style.borderColor = 'rgba(255,59,48,0.2)';
    }
});

(function() {
    const btn = document.getElementById('detailWishBtn');
    if (!btn) return;

    const wish = getWish();
    const id = btn.dataset.id;

    if (wish[id]) {
        btn.querySelector('i').className = 'fa-solid fa-heart';
        btn.style.color = '#ff3b30';
        btn.style.background = 'rgba(255,59,48,0.08)';
        btn.style.borderColor = 'rgba(255,59,48,0.2)';
    }
})(); 
