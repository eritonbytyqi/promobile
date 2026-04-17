// resources/js/admin/products-create.js

// ════════════ FOTO KRYESORE TË PRODUKTIT ════════════
let selectedFiles = [];
const existingCount = window.PRODUCT_DATA?.existingImagesCount ?? 0;

function dzHover(on) {
    document.getElementById('dropzone')?.classList.toggle('hover', on);
}
function handleDrop(e) {
    e.preventDefault();
    dzHover(false);
    const dropped = Array.from(e.dataTransfer.files || []).filter(f => f.type.startsWith('image/'));
    if (dropped.length) addFiles(dropped);
}
function addFiles(incoming) {
    Array.from(incoming).forEach(f => {
        const exists = selectedFiles.some(s => s.name === f.name && s.size === f.size && s.lastModified === f.lastModified);
        if (!exists) selectedFiles.push(f);
    });
    syncInputFiles();
    renderPreviews();
}
function syncInputFiles() {
    const input = document.getElementById('imagesInput');
    if (!input) return;
    const dt = new DataTransfer();
    selectedFiles.forEach(f => dt.items.add(f));
    input.files = dt.files;
}
function renderPreviews() {
    const section = document.getElementById('previewSection');
    const list    = document.getElementById('previewList');
    const countEl = document.getElementById('previewCount');
    const selPrim = document.getElementById('primarySelect');
    if (!section || !list || !countEl || !selPrim) return;
    updateStats();
    if (!selectedFiles.length) {
        section.style.display = 'none';
        list.innerHTML = '';
        selPrim.innerHTML = '';
        return;
    }
    section.style.display = 'block';
    countEl.textContent = selectedFiles.length + ' foto';
    list.innerHTML = '';
    selPrim.innerHTML = '';
    selectedFiles.forEach((file, i) => {
        const div = document.createElement('div');
        div.className = 'prev-item' + (i === 0 ? ' primary' : '');
        div.id = `prev_${i}`;
        const reader = new FileReader();
        reader.onload = ev => {
            div.innerHTML = `<img src="${ev.target.result}" alt="${file.name}">
                ${i === 0 ? '<div class="p-label"><i class="fa-solid fa-star" style="font-size:8px;"></i> KRYESORE</div>' : ''}
                <div class="prev-actions"><button type="button" class="prev-rm-btn" onclick="removeFile(${i})"><i class="fa-solid fa-xmark"></i></button></div>`;
        };
        reader.readAsDataURL(file);
        list.appendChild(div);
        const opt = document.createElement('option');
        opt.value = i;
        opt.textContent = `Foto ${i + 1} — ${file.name.substring(0, 22)}`;
        selPrim.appendChild(opt);
    });
    selPrim.value = "0";
}
function removeFile(idx) {
    selectedFiles.splice(idx, 1);
    syncInputFiles();
    renderPreviews();
}
function clearPreviews() {
    selectedFiles = [];
    const input = document.getElementById('imagesInput');
    if (input) input.value = '';
    const section = document.getElementById('previewSection');
    const list    = document.getElementById('previewList');
    const primary = document.getElementById('primarySelect');
    if (section) section.style.display = 'none';
    if (list)    list.innerHTML = '';
    if (primary) primary.innerHTML = '';
    updateStats();
}
function updateStats() {
    const n  = selectedFiles.length;
    const sN = document.getElementById('statNew');
    const sT = document.getElementById('statTotal');
    if (sN) sN.textContent = n;
    if (sT) sT.textContent = existingCount + n;
}
document.addEventListener('change', function (e) {
    if (e.target.id === 'imagesInput') addFiles(e.target.files);
    if (e.target.id === 'primarySelect') {
        const idx = parseInt(e.target.value, 10);
        document.querySelectorAll('.prev-item').forEach((el, i) => {
            el.classList.toggle('primary', i === idx);
            const lbl = el.querySelector('.p-label');
            if (i === idx && !lbl) el.insertAdjacentHTML('beforeend', '<div class="p-label"><i class="fa-solid fa-star" style="font-size:8px;"></i> KRYESORE</div>');
            else if (i !== idx && lbl) lbl.remove();
        });
    }
});

// ════════════ VARIANTET ════════════
let variantIndex = window.PRODUCT_DATA?.variantCount ?? 0;
const storageCounters   = {};
const variantPhotoFiles = {};

function updateSwatch(vi, color) {
    const swatch = document.getElementById(`swatchPreview_${vi}`);
    if (swatch) swatch.style.background = color;
}
function addVariant() {
    const list  = document.getElementById('variantsList');
    const empty = document.getElementById('variantsEmpty');
    if (empty) empty.remove();
    const vi = variantIndex++;
    variantPhotoFiles[vi] = [];
    storageCounters[vi]   = 0;
    const wrap = document.createElement('div');
    wrap.className = 'variant-color-group';
    wrap.id = `variant_${vi}`;
    wrap.innerHTML = `
<div class="variant-color-head">
    <div class="color-swatch-preview" id="swatchPreview_${vi}" style="background:#cccccc;"></div>
    <div style="flex:1;">
        <label class="form-label" style="margin-bottom:6px;">Ngjyra</label>
        <div class="quick-colors">
            ${['#000000','#ffffff','#c0c0c0','#ffd700','#ff6b35','#e74c3c','#2ecc71','#3498db','#9b59b6','#1abc9c']
                .map(c => `<span class="quick-color" style="background:${c};" onclick="document.getElementById('colorHex_${vi}').value='${c}'; updateSwatch(${vi}, '${c}');" title="${c}"></span>`)
                .join('')}
        </div>
        <div class="color-picker-row">
            <input type="color" id="colorHex_${vi}" name="variants[${vi}][color_hex]" value="#cccccc" oninput="updateSwatch(${vi}, this.value)">
            <input type="text" name="variants[${vi}][color_name]" class="form-control" placeholder="p.sh. Black, Midnight, Silver">
        </div>
    </div>
    <button type="button" class="rm-btn" onclick="removeVariant(${vi})"><i class="fa-solid fa-xmark"></i></button>
</div>
<div class="variant-body">
    <div class="color-photos-section">
        <div class="color-photos-header">
            <div class="label"><i class="fa-solid fa-images" style="color:var(--accent3);"></i> Fotot e Ngjyrës</div>
            <button type="button" onclick="document.getElementById('colorPhotoInput_${vi}').click()" class="btn btn-success-soft btn-sm"><i class="fa-solid fa-plus"></i> Shto Foto</button>
            <input type="file" id="colorPhotoInput_${vi}" name="variants[${vi}][images][]" accept="image/*" multiple hidden onchange="addColorPhotos(this, ${vi})">
        </div>
        <div class="color-photos-grid" id="colorPhotosGrid_${vi}">
            <div class="color-photo-add-btn" onclick="document.getElementById('colorPhotoInput_${vi}').click()"><i class="fa-solid fa-plus"></i><span>Shto Foto</span></div>
        </div>
    </div>
    <div class="storage-section">
        <div class="storage-section-header">
            <div class="label"><i class="fa-solid fa-hard-drive" style="color:#7c3aed;"></i> Storage / Çmimi / Stoku</div>
            <button type="button" onclick="addStorageRow(${vi})" class="btn btn-secondary btn-sm"><i class="fa-solid fa-plus"></i> Shto Storage</button>
        </div>
        <div class="storage-col-headers"><span>Storage</span><span>Bazë (€)</span><span>Shtesë (€)</span><span>Totali</span><span>Stoku</span><span></span></div>
        <div class="storage-rows" id="storageRows_${vi}">
            <div class="storage-empty" id="storageEmpty_${vi}">Nuk ka storage. Kliko "Shto Storage".</div>
        </div>
    </div>
</div>`;
    list.appendChild(wrap);
}
function removeVariant(vi) {
    document.getElementById(`variant_${vi}`)?.remove();
    delete variantPhotoFiles[vi];
    delete storageCounters[vi];
    const list = document.getElementById('variantsList');
    if (!list.querySelector('.variant-color-group')) {
        list.innerHTML = `<div id="variantsEmpty" class="variant-empty"><i class="fa-solid fa-palette" style="font-size:28px;opacity:0.2;display:block;margin-bottom:10px;"></i>Nuk ka variante. Kliko <strong>"Shto Ngjyrë"</strong> për të shtuar ngjyrë me foto dhe storage.</div>`;
    }
}
function syncColorPhotoInput(vi) {
    const input = document.getElementById(`colorPhotoInput_${vi}`);
    if (!input) return;
    const dt = new DataTransfer();
    (variantPhotoFiles[vi] || []).forEach(file => dt.items.add(file));
    input.files = dt.files;
}
function refreshPrimaryBadges(vi) {
    const grid = document.getElementById(`colorPhotosGrid_${vi}`);
    if (!grid) return;
    Array.from(grid.querySelectorAll('.color-photo-item')).forEach((el, i) => {
        el.classList.toggle('primary-photo', i === 0);
        const badge = el.querySelector('.color-photo-primary-badge');
        if (i === 0 && !badge) el.insertAdjacentHTML('beforeend', '<span class="color-photo-primary-badge">★ Kryesore</span>');
        else if (i !== 0 && badge) badge.remove();
    });
}
function renderColorPhotos(vi) {
    const grid = document.getElementById(`colorPhotosGrid_${vi}`);
    if (!grid) return;
    grid.querySelectorAll('.color-photo-item.new-upload').forEach(el => el.remove());
    const addBtn = grid.querySelector('.color-photo-add-btn');
    (variantPhotoFiles[vi] || []).forEach((file, idx) => {
        const reader = new FileReader();
        reader.onload = function (e) {
            const item = document.createElement('div');
            item.className = 'color-photo-item new-upload';
            item.dataset.vi  = vi;
            item.dataset.idx = idx;
            item.innerHTML = `<img src="${e.target.result}" alt=""><button type="button" class="color-photo-rm" onclick="removeColorPhoto(this)"><i class="fa-solid fa-xmark"></i></button>`;
            if (addBtn) grid.insertBefore(item, addBtn);
            else grid.appendChild(item);
            refreshPrimaryBadges(vi);
        };
        reader.readAsDataURL(file);
    });
    setTimeout(() => refreshPrimaryBadges(vi), 30);
}
function addColorPhotos(input, vi) {
    if (!input.files || !input.files.length) return;
    if (!variantPhotoFiles[vi]) variantPhotoFiles[vi] = [];
    Array.from(input.files).forEach(file => {
        const exists = variantPhotoFiles[vi].some(f => f.name === file.name && f.size === file.size && f.lastModified === file.lastModified);
        if (!exists) variantPhotoFiles[vi].push(file);
    });
    input.value = '';
    syncColorPhotoInput(vi);
    renderColorPhotos(vi);
}
function removeColorPhoto(btn) {
    const item = btn.closest('.color-photo-item');
    if (!item) return;
    const vi = parseInt(item.dataset.vi, 10);
    if (item.querySelector('input[type="hidden"]')) { item.remove(); refreshPrimaryBadges(vi); return; }
    const idx = parseInt(item.dataset.idx, 10);
    if (!isNaN(vi) && !isNaN(idx) && variantPhotoFiles[vi]) {
        variantPhotoFiles[vi].splice(idx, 1);
        syncColorPhotoInput(vi);
        renderColorPhotos(vi);
        return;
    }
    item.remove();
    refreshPrimaryBadges(vi);
}
function addStorageRow(vi) {
    // ✅ FIX: storageCounters inicializohet nga numri real i rows ekzistuese
    if (storageCounters[vi] === undefined) {
        const container = document.getElementById(`storageRows_${vi}`);
        storageCounters[vi] = container
            ? container.querySelectorAll('.storage-row').length
            : document.querySelectorAll(`#variant_${vi} .storage-row`).length;
    }
    const si      = storageCounters[vi]++;
    const isFirst = si === 0;
    const container = document.getElementById(`storageRows_${vi}`);
    if (!container) return;
    document.getElementById(`storageEmpty_${vi}`)?.remove();
    const row = document.createElement('div');
    row.className = 'storage-row';
    row.id = `storageRow_${vi}_${si}`;
    row.innerHTML = `<select name="variants[${vi}][storages][${si}][storage]" class="form-select"><option value="">— Zgjedh —</option><option value="64GB">64GB</option><option value="128GB">128GB</option><option value="256GB">256GB</option><option value="512GB">512GB</option><option value="1TB">1TB</option><option value="2TB">2TB</option></select>${isFirst ? `<input type="number" step="0.01" name="variants[${vi}][storages][${si}][base_price]" class="form-control storage-base" id="basePrice_${vi}" placeholder="999.00" oninput="syncBasePrice(${vi}); calcTotal(${vi}, ${si})">` : `<div style="font-size:12px;color:var(--text-muted);align-self:center;padding:0 4px;"><i class="fa-solid fa-arrow-turn-down-right" style="font-size:10px;"></i><span id="baseDisplay_${vi}_${si}" style="font-weight:600;">—</span></div><input type="hidden" name="variants[${vi}][storages][${si}][base_price]" id="basePriceHidden_${vi}_${si}" value="0">`}<input type="number" step="0.01" name="variants[${vi}][storages][${si}][extra_price]" class="form-control" placeholder="+0.00" oninput="calcTotal(${vi}, ${si})"><div style="font-size:13px;color:#059669;font-weight:700;align-self:center;" id="total_${vi}_${si}">—</div><input type="number" name="variants[${vi}][storages][${si}][stock]" class="form-control" value="0" placeholder="0"><button type="button" class="rm-btn-sm" onclick="removeStorageRow(this)"><i class="fa-solid fa-xmark"></i></button>`;
    container.appendChild(row);
}
function removeStorageRow(btn) {
    const row = btn.closest('.storage-row');
    if (!row) return;
    const vi = row.id.split('_')[1];
    row.remove();
    const container = document.getElementById(`storageRows_${vi}`);
    if (container && !container.querySelector('.storage-row')) {
        container.innerHTML = `<div class="storage-empty" id="storageEmpty_${vi}">Nuk ka storage. Kliko "Shto Storage".</div>`;
    }
}

// ════════════ SPECS ════════════
let specIndex = window.PRODUCT_DATA?.specCount ?? 0;
function addSpec(keyName = '') {
    document.getElementById('specsEmpty')?.style.setProperty('display', 'none');
    const si  = specIndex++;
    const row = document.createElement('div');
    row.className = 'spec-row';
    row.id = `specRow_${si}`;
    row.innerHTML = `<input type="text" name="specs[${si}][key]" class="form-control" value="${keyName}" placeholder="p.sh. Ekrani"><input type="text" name="specs[${si}][value]" class="form-control" placeholder="p.sh. 6.7&quot; OLED"><button type="button" onclick="removeSpec(this)" class="rm-btn"><i class="fa-solid fa-xmark"></i></button>`;
    document.getElementById('specsList').appendChild(row);
    setTimeout(() => { const inputs = row.querySelectorAll('input'); (keyName ? inputs[1] : inputs[0])?.focus(); }, 50);
}
function removeSpec(button) {
    button.closest('.spec-row')?.remove();
    if (!document.querySelectorAll('#specsList .spec-row').length) {
        const empty = document.getElementById('specsEmpty');
        if (empty) empty.style.display = 'block';
    }
}

// ════════════ FOTO EKZISTUESE ════════════
function deleteImage(imgId, productId) {
    if (!confirm('Fshi këtë foto?')) return;
    fetch(`/admin/products/${productId}/images/${imgId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
    })
    .then(r => r.json()).then(data => {
        if (!data.success) return;
        const el = document.getElementById(`imgWrap${imgId}`);
        if (!el) return;
        el.style.cssText += ';opacity:0;transform:scale(0.8);transition:all .3s;';
        setTimeout(() => {
            el.remove();
            const sE = document.getElementById('statExisting');
            if (sE) sE.textContent = document.querySelectorAll('[id^="imgWrap"]').length;
            updateStats();
        }, 300);
    });
}
function setPrimary(imgId, productId) {
    fetch(`/admin/products/${productId}/images/${imgId}/primary`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
    })
    .then(r => r.json()).then(data => {
        if (!data.success) return;
        document.querySelectorAll('.existing-primary-badge').forEach(b => b.remove());
        document.querySelectorAll('.existing-img-card').forEach(c => c.classList.remove('is-primary'));
        const wrap = document.getElementById(`imgWrap${imgId}`);
        if (wrap) {
            wrap.classList.add('is-primary');
            const badge = document.createElement('span');
            badge.className = 'existing-primary-badge';
            badge.innerHTML = '<i class="fa-solid fa-star" style="font-size:8px;"></i> KRYESORE';
            wrap.appendChild(badge);
            wrap.querySelector('.img-action-primary')?.remove();
        }
    });
}

// ════════════ NËNKATEGORITË ════════════
function renderSubcategorySuggestions(categoryId) {
    const group  = document.getElementById('subcategoryGroup');
    const select = document.getElementById('subcategorySelect');
    if (!group || !select) return;

    select.innerHTML = '<option value="">— Zgjedh nënkategorinë —</option>';
    group.style.display = 'none';

    if (!categoryId) return;

    fetch(`/admin/categories/${categoryId}/subcategories`)
        .then(r => r.json())
        .then(subcategories => {
            if (!subcategories.length) return;
            const currentVal = window.PRODUCT_DATA?.subcategory ?? '';
            subcategories.forEach(name => {
                const opt = document.createElement('option');
                opt.value = name;
                opt.textContent = name;
                if (name === currentVal) opt.selected = true;
                select.appendChild(opt);
            });
            group.style.display = '';
        })
        .catch(() => {});
}

// ════════════ DOMContentLoaded ════════════
document.addEventListener('DOMContentLoaded', function () {

    // ✅ FIX: inicializimi i storageCounters bazuar tek storageRows container (tani ekziston në edit mode)
    document.querySelectorAll('.variant-color-group').forEach(group => {
        const vi = parseInt((group.id || '').split('_')[1], 10);
        if (isNaN(vi)) return;
        const container = document.getElementById(`storageRows_${vi}`);
        storageCounters[vi] = container
            ? container.querySelectorAll('.storage-row').length
            : group.querySelectorAll('.storage-row').length;
        variantPhotoFiles[vi] = [];
    });

    const categorySelect  = document.getElementById('categorySelect');
    const brandSelect     = document.getElementById('brandSelect');
    const linkedCard      = document.getElementById('linkedProductsCard');
    const accessoryCatIds = (window.ACCESSORY_CAT_IDS || []).map(String);

    function toggleLinkedCard() {
        if (!linkedCard) return;
        const isAccessory = accessoryCatIds.includes(String(categorySelect?.value || ''));
        linkedCard.style.display = isAccessory ? '' : 'none';
    }

    if (categorySelect) {
        categorySelect.addEventListener('change', function () {
            const categoryId     = this.value;
            const currentBrandId = window.PRODUCT_DATA?.brandId ?? null;

            if (brandSelect) {
                brandSelect.innerHTML = '<option value="">— Zgjedh brendin —</option>';
                if (categoryId) {
                    fetch('/admin/get-brands-by-category/' + categoryId)
                        .then(r => r.json())
                        .then(brands => {
                            brands.forEach(brand => {
                                const opt = document.createElement('option');
                                opt.value = brand.id;
                                opt.textContent = brand.name;
                                if (brand.id === currentBrandId) opt.selected = true;
                                brandSelect.appendChild(opt);
                            });
                        })
                        .catch(() => {});
                }
            }

            renderSubcategorySuggestions(categoryId);
            toggleLinkedCard();
        });

        if (categorySelect.value) categorySelect.dispatchEvent(new Event('change'));
    }

    toggleLinkedCard();

    document.getElementById('linkedList')?.addEventListener('change', function (e) {
        if (e.target.name !== 'linked_product_ids[]') return;
        e.target.closest('.acc-row')?.classList.toggle('acc-row--on', e.target.checked);
        const total   = document.querySelectorAll('input[name="linked_product_ids[]"]:checked').length;
        const badge   = document.getElementById('linkedBadge');
        const countEl = document.getElementById('linkedCount');
        if (countEl) countEl.textContent = total;
        if (badge)   badge.style.display  = total > 0 ? 'inline-block' : 'none';
    });

    window.linkedFilter = function () {
        const q = (document.getElementById('linkedSearch')?.value || '').toLowerCase().trim();
        document.querySelectorAll('#linkedList .acc-row').forEach(row => {
            row.style.display = row.dataset.name.includes(q) ? '' : 'none';
        });
    };

    window.syncBasePrice = function (vi) {
        const base = parseFloat(document.getElementById(`basePrice_${vi}`)?.value || 0);
        const container = document.getElementById(`storageRows_${vi}`);
        if (!container) return;
        container.querySelectorAll('.storage-row').forEach((row, idx) => {
            if (idx === 0) return;
            const nameAttr = row.querySelector('[name*="extra_price"]')?.getAttribute('name');
            if (!nameAttr) return;
            const match = nameAttr.match(/storages\]\[(\d+)\]/);
            if (!match) return;
            const si = match[1];
            const hidden = document.getElementById(`basePriceHidden_${vi}_${si}`);
            if (hidden) hidden.value = base;
            const display = document.getElementById(`baseDisplay_${vi}_${si}`);
            if (display) display.textContent = base > 0 ? base.toFixed(2) + ' €' : '—';
            calcTotal(vi, parseInt(si));
        });
    };

    window.calcTotal = function (vi, si) {
        const base  = parseFloat(document.getElementById(`basePrice_${vi}`)?.value || 0);
        const row   = document.getElementById(`storageRow_${vi}_${si}`);
        if (!row) return;
        const extra = parseFloat(row.querySelector('[name*="extra_price"]')?.value || 0);
        const el    = document.getElementById(`total_${vi}_${si}`);
        if (el) el.textContent = (base + extra) > 0 ? `${(base + extra).toFixed(2)} €` : '—';
    };

    window.refreshAllTotals = function (vi) {
        const container = document.getElementById(`storageRows_${vi}`);
        if (!container) return;
        container.querySelectorAll('.storage-row').forEach(row => {
            const extraInput = row.querySelector('[name*="extra_price"]');
            if (!extraInput) return;
            const match = extraInput.getAttribute('name').match(/storages\]\[(\d+)\]/);
            if (match) calcTotal(vi, parseInt(match[1]));
        });
    };

});