(function () {
    const state = window.ProductDetailState;
    const data = window.ProductData;
    const helpers = window.ProductHelpers;
    const gallery = window.ProductGallery;

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
            const extra = Number(sv.extra_price ?? 0);
            const isOut = Number(sv.stock || 0) <= 0;
            const isActive = String(sv.id) === String(state.selVariantId) || (!state.selVariantId && i === 0);

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

            if (!isOut) {
                btn.addEventListener('click', () => selectStorage(btn));
            }

            grid.appendChild(btn);
        });
    }

    function refreshUI() {
        const group = helpers.getGroup(state.selColorName);
        const storage = helpers.getStorage(group, state.selVariantId);

        const groupBasePrice = group?.storages?.[0]?.base_price != null
            ? Number(group.storages[0].base_price)
            : data.BASE_PRICE;

        const extraPrice = Number(storage?.extra_price ?? 0);

        const finalPrice = storage
            ? Number(storage.sale_price ?? (groupBasePrice + extraPrice))
            : data.BASE_PRICE;

        const stock = storage ? Number(storage.stock || 0) : data.BASE_STOCK;

        const priceEl = document.getElementById('priceMain');
        if (priceEl) {
            priceEl.style.opacity = '0';

            setTimeout(() => {
                const hasVariants = data.colorGroups.length > 0;

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

        if (data.BASE_OLD && oldEl && badgeEl) {
            const pct = Math.round(((data.BASE_OLD - finalPrice) / data.BASE_OLD) * 100);
            oldEl.textContent = data.BASE_OLD.toFixed(2) + ' €';
            oldEl.style.display = '';
            badgeEl.textContent = '-' + pct + '%';
            badgeEl.style.display = '';
        } else {
            if (oldEl) oldEl.style.display = 'none';
            if (badgeEl) badgeEl.style.display = 'none';
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
            if (stock <= 0) {
                btn.classList.add('disabled');
                btn.disabled = true;
                btnTxt.textContent = 'Jashtë Stoku';
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
            if (group.color_name) parts.push(group.color_name);
            if (storage.storage) parts.push(storage.storage);

            toastTxt.innerHTML = '✓ ' + parts.join(' · ') + ' — <strong>' + finalPrice.toFixed(2) + ' €</strong>';
        } else {
            toast.classList.remove('ok');
            toastTxt.innerHTML = '✓ ' + (group?.color_name || 'Ngjyra') + ' — zgjidh storage';
        }
    }

    function selectStorage(el) {
        const variantId = parseInt(el.dataset.variantId, 10);
        if (!variantId) return;

        state.selVariantId = variantId;
        helpers.setOrderButtonDefault();

        document.querySelectorAll('.pm-storage').forEach(s => s.classList.remove('active'));
        el.classList.add('active');

        const group = helpers.getGroup(state.selColorName);
        const storage = group?.storages?.find(s => String(s.id) === String(variantId));
        const lbl = document.getElementById('storageLabel');
        if (lbl && storage) lbl.textContent = storage.storage || '';

        refreshUI();
    }

    function selectColor(el, name) {
        if (state.selColorName === name && el.classList.contains('active')) {
            state.selColorName = null;
            state.selVariantId = null;

            el.classList.remove('active');
            renderStorages(null);
            gallery.renderColorGallery(null);
            refreshUI();
            return;
        }

        state.selColorName = name;
        state.selVariantId = null;
        helpers.setOrderButtonDefault();

        document.querySelectorAll('.pm-color').forEach(c => c.classList.remove('active'));
        el.classList.add('active');

        const lbl = document.getElementById('colorLabel');
        if (lbl) lbl.textContent = name;

        const group = helpers.getGroup(name);
        const firstAvail = group?.storages?.find(s => Number(s.stock || 0) > 0) || group?.storages?.[0] || null;

        if (firstAvail) {
            state.selVariantId = firstAvail.id;
        }

        renderStorages(group);
        gallery.renderColorGallery(group);

        document.querySelectorAll('#productThumbs .pm-thumb').forEach(b => b.classList.remove('active'));

        if (group && group.images && group.images.length > 0) {
            gallery.switchMainImgDirect(group.images[0]);
        }

        refreshUI();
    }

    window.ProductVariants = {
        renderStorages,
        refreshUI,
        selectStorage,
        selectColor
    };

    window.selectColor = selectColor;
})();