/* Admin Pages JS — logjike specifike
   resources/js/admin/pages.js
*/

/* === Banners/create.blade.php === */
function previewBannerImage(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = ev => {
        const bg = document.getElementById('previewBg');
        if (bg) {
            bg.style.backgroundImage = `url('${ev.target.result}')`;
            document.getElementById('noImgHint').style.display = 'none';
        }
    };
    reader.readAsDataURL(input.files[0]);
}

function setSwatchColor(hex, el) {
    document.getElementById('bgColorPicker').value = hex;
    document.getElementById('bgColorText').value   = hex;
    document.getElementById('bannerPreview').style.backgroundColor = hex;
    document.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('active'));
    el.classList.add('active');
}

function setBadge(txt) {
    const input = document.querySelector('input[name="banner_badge"]');
    if (input) input.value = txt;
    document.getElementById('previewBadge').textContent = txt;
}

/* === Stock/index.blade.php === */
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

function toggleCard(id) {
    document.getElementById(`stcard_${id}`).classList.toggle('open');
}

function ch(key, d) {
    const inp = document.getElementById(`input_${key}`);
    inp.value = Math.max(0, parseInt(inp.value || 0) + d);
}

async function saveVariant(id) {
    const stock = parseInt(document.getElementById(`input_v${id}`).value);
    await doSave(`/admin/stock/variant/${id}`, { stock }, `btn_v${id}`);
}

async function saveProduct(id) {
    const stock = parseInt(document.getElementById(`input_p${id}`).value);
    await doSave(`/admin/stock/product/${id}`, { stock }, `btn_p${id}`);
}

async function doSave(url, body, btnId) {
    const btn = document.getElementById(btnId);
    const orig = btn.textContent;
    btn.textContent = '...';
    btn.disabled = true;
    try {
        const res  = await fetch(url, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN':  CSRF,
                'Accept':        'application/json',
            },
            body: JSON.stringify(body),
        });
        const data = await res.json();
        if (data.success) {
            btn.textContent = '✓ Ruajtur';
            btn.classList.add('saved');
            setTimeout(() => {
                btn.textContent = orig;
                btn.classList.remove('saved');
                btn.disabled = false;
            }, 2000);
        }
    } catch(e) {
        btn.textContent = 'Gabim';
        btn.disabled = false;
    }
}

/* === admin/settings.blade.php === */
/* ── Quill editors ── */
const toolbarOpts = [
    ['bold','italic','underline'],
    [{'header':2},{'header':3}],
    [{'list':'ordered'},{'list':'bullet'}],
    ['link'],['clean']
];

const termsQuill = new Quill('#terms_editor', {
    theme: 'snow',
    placeholder: 'Shkruaj kushtet e përdorimit këtu...',
    modules: { toolbar: toolbarOpts }
});

const privacyQuill = new Quill('#privacy_editor', {
    theme: 'snow',
    placeholder: 'Shkruaj politikën e privatësisë këtu...',
    modules: { toolbar: toolbarOpts }
});

const termsContent   = @json($settings['terms_content']   ?? '');
const privacyContent = @json($settings['privacy_content'] ?? '');
if (termsContent)   termsQuill.root.innerHTML   = termsContent;
if (privacyContent) privacyQuill.root.innerHTML = privacyContent;

document.querySelector('#panel-terms form').addEventListener('submit', function () {
    this.querySelector('textarea[name="terms_content"]').value = termsQuill.root.innerHTML;
});
document.querySelector('#panel-privacy form').addEventListener('submit', function () {
    this.querySelector('textarea[name="privacy_content"]').value = privacyQuill.root.innerHTML;
});

/* ── Tab switching ── */
function swTab(id, el) {
    document.querySelectorAll('.sw-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.sw-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('panel-' + id).classList.add('active');
    el.classList.add('active');
}

/* ── Locations ── */
let locIdx = document.querySelectorAll('.loc-card').length;

function addLoc() {
    const wrap = document.getElementById('locationsWrap');
    const num  = wrap.querySelectorAll('.loc-card').length + 1;
    const i    = locIdx++;

    const html = `
    <div class="loc-card" data-index="${i}">
        <div class="loc-head">
            <div class="loc-label">
                <span>Lokacioni</span>
                <span class="loc-badge loc-number">${num}</span>
            </div>
            <button type="button" class="btn-remove" onclick="removeLoc(this)">
                 Largo
            </button>
        </div>
        <div class="sw-grid2 mb12">
            <div class="sw-field">
                <label>Emri / Qyteti</label>
                <input type="text" name="locations[${i}][name]" placeholder="Prishtinë">
            </div>
            <div class="sw-field">
                <label>Telefoni</label>
                <input type="text" name="locations[${i}][phone]" placeholder="+383 44 000 000">
            </div>
        </div>
        <div class="sw-field mb10">
            <label>Adresa e Plotë</label>
            <input type="text" class="loc-addr-full"
                   name="locations[${i}][address_full]"
                   placeholder="Rr. Nënë Tereza, Nr.15, Prishtinë"
                   oninput="updateLocMap(this)">
        </div>
        <div class="sw-field">
            <label>Adresa e Shkurtër</label>
            <input type="text" name="locations[${i}][address_short]" placeholder="Prishtinë, Kosovë">
        </div>
        <div class="map-preview loc-map-wrap" style="display:none">
            <iframe class="loc-map-iframe" loading="lazy"></iframe>
        </div>
    </div>`;

    wrap.insertAdjacentHTML('beforeend', html);
    refreshLocNums();
}

function removeLoc(btn) {
    const wrap  = document.getElementById('locationsWrap');
    const cards = wrap.querySelectorAll('.loc-card');
    if (cards.length <= 1) { return; }
    btn.closest('.loc-card').remove();
    reindexLocs();
    refreshLocNums();
}

function refreshLocNums() {
    document.querySelectorAll('#locationsWrap .loc-card').forEach((c, i) => {
        const el = c.querySelector('.loc-number');
        if (el) el.textContent = i + 1;
    });
}

function reindexLocs() {
    document.querySelectorAll('#locationsWrap .loc-card').forEach((card, i) => {
        card.setAttribute('data-index', i);
        card.querySelectorAll('input').forEach(inp => {
            const n = inp.getAttribute('name');
            if (n) inp.setAttribute('name', n.replace(/locations\[\d+\]/, `locations[${i}]`));
        });
    });
}

function updateLocMap(input) {
    const card   = input.closest('.loc-card');
    const wrap   = card.querySelector('.loc-map-wrap');
    const iframe = card.querySelector('.loc-map-iframe');
    const val    = input.value.trim();

    clearTimeout(input._mapTimer);

    if (!val) { wrap.style.display = 'none'; iframe.src = ''; return; }

    input._mapTimer = setTimeout(() => {
        iframe.src = 'https://maps.google.com/maps?q=' + encodeURIComponent(val) + '&output=embed';
        wrap.style.display = 'block';
    }, 700);
}

/* === brands/index.blade.php === */
function filterTable() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('#brandsTable tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}
function toggleBrand(div) {
    const input = div.querySelector('input[type="checkbox"]');
    const box   = div.querySelector('.brand-checkbox-box');
    const check = div.querySelector('.brand-checkbox-check');

    input.checked = !input.checked;

    if (input.checked) {
        div.style.borderColor = '#00bcd4';
        div.style.background  = 'rgba(0,188,212,0.06)';
        box.style.background  = '#00bcd4';
        box.style.borderColor = '#00bcd4';
        check.style.display   = '';
    } else {
        div.style.borderColor = '';
        div.style.background  = '';
        box.style.background  = 'transparent';
        box.style.borderColor = '';
        check.style.display   = 'none';
    }
}

/* === categories/index.blade.php === */
function generateSlug(val) {
    const slug = val.toLowerCase()
        .replace(/ë/g,'e').replace(/ç/g,'c')
        .replace(/[^a-z0-9\s-]/g,'')
        .trim().replace(/\s+/g,'-');
    const field = document.getElementById('slugField');
    if (field && !field.dataset.edited) field.value = slug;
    const preview = document.getElementById('slugPreview');
    if (preview) preview.textContent = slug || '...';
}

function previewIcon(val) {
    val = val.trim().replace(/^fa-/, '');
    const full = 'fa-' + (val || 'tag');
    const iconEl = document.getElementById('iconPreviewI');
    const hiddenEl = document.getElementById('iconHidden');
    if (iconEl) iconEl.className = 'fa-solid ' + full;
    if (hiddenEl) hiddenEl.value = full;
}

function updateBrandCard(input) {
    const label = input.closest('label');
    const box   = label.querySelector('.brand-checkbox-box');
    const check = label.querySelector('.brand-checkbox-check');

    if (input.checked) {
        label.style.borderColor = '#00bcd4';
        label.style.background  = 'rgba(0,188,212,0.06)';
        box.style.background    = '#00bcd4';
        box.style.borderColor   = '#00bcd4';
        check.style.display     = '';
    } else {
        label.style.borderColor = '';
        label.style.background  = '';
        box.style.background    = 'transparent';
        box.style.borderColor   = '';
        check.style.display     = 'none';
    }
}

/* === orders/create.blade.php === */
let itemCount = {{ isset($order) ? $order->items->count() : 1 }};
const productsData = @json($products->map(fn($p) => ['id'=>$p->id,'name'=>$p->name,'price'=>$p->price]));

function addItem() {
    const i = itemCount++;
    const opts = productsData.map(p => `<option value="${p.id}" data-price="${p.price}">${p.name}</option>`).join('');
    const row = document.createElement('div');
    row.className = 'order-item-row';
    row.style.cssText = 'display:grid;grid-template-columns:1fr 100px 110px 36px;gap:12px;padding:16px 20px;border-bottom:1px solid var(--border);';
    row.innerHTML = `
        <div>
            <label class="form-label">Produkti</label>
            <select name="items[${i}][product_id]" class="form-select" onchange="updatePrice(this,${i})">
                <option value="">— Zgjedh produktin —</option>${opts}
            </select>
        </div>
        <div>
            <label class="form-label">Sasia</label>
            <input type="number" name="items[${i}][quantity]" class="form-control item-qty" value="1" min="1" onchange="calcTotal()">
        </div>
        <div>
            <label class="form-label">Çmimi (€)</label>
            <input type="number" name="items[${i}][price]" class="form-control item-price" step="0.01" value="0.00" onchange="calcTotal()">
        </div>
        <div style="display:flex;align-items:flex-end;">
            <button type="button" class="btn btn-danger btn-sm btn-icon" onclick="removeItem(this)">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>`;
    document.getElementById('orderItems').appendChild(row);
}

function removeItem(btn) {
    const rows = document.querySelectorAll('.order-item-row');
    if (rows.length > 1) { btn.closest('.order-item-row').remove(); calcTotal(); }
}

function updatePrice(sel, i) {
    const opt = sel.options[sel.selectedIndex];
    const price = opt.dataset.price || 0;
    const row = sel.closest('.order-item-row');
    row.querySelector('.item-price').value = parseFloat(price).toFixed(2);
    calcTotal();
}

/* === orders/index.blade.php === */
function toggleAll(cb) {
    document.querySelectorAll('.row-check').forEach(c => {
        c.checked = cb.checked;
        c.closest('tr,.order-mobile-card')?.classList.toggle('selected', cb.checked);
    });
    updateBulkBar();
}

function updateBulkBar() {
    const checked = document.querySelectorAll('.row-check:checked');
    document.getElementById('bulkCount').textContent = checked.length;
    document.getElementById('bulkBar').classList.toggle('show', checked.length > 0);
    const container = document.getElementById('bulkInputs');
    container.innerHTML = '';
    checked.forEach(c => {
        const inp = document.createElement('input');
        inp.type='hidden'; inp.name='ids[]'; inp.value=c.value;
        container.appendChild(inp);
    });
    const all = document.querySelectorAll('.row-check');
    const sel = document.getElementById('selectAll');
    if (sel) {
        sel.indeterminate = checked.length > 0 && checked.length < all.length;
        sel.checked = checked.length === all.length && all.length > 0;
    }
    document.querySelectorAll('.row-check').forEach(c => {
        c.closest('tr')?.classList.toggle('selected', c.checked);
    });
}

function clearSelection() {
    document.querySelectorAll('.row-check').forEach(c => c.checked=false);
    const sel = document.getElementById('selectAll');
    if (sel) sel.checked=false;
    updateBulkBar();
}

function confirmBulk() {
    const count = document.querySelectorAll('.row-check:checked').length;
    return confirm('A jeni i sigurt? Do të fshihen ' + count + ' porosi permanentisht!');
}

/* === orders/show.blade.php === */
function updateRefundTotal() {
    let total = 0;
    document.querySelectorAll('input[name^="items"][name$="[quantity]"]').forEach(input => {
        const qty = parseInt(input.value) || 0;
        const row = input.closest('[style*="background:var(--surface2)"]');
        if (row) {
            const priceEl = row.querySelector('input[name$="[unit_price]"]');
            if (priceEl) {
                total += qty * parseFloat(priceEl.value);
            }
        }
    });
    document.getElementById('refundTotal').textContent = total.toFixed(2) + ' €';
}

function confirmRefund() {
    const total = document.getElementById('refundTotal').textContent;
    if (total === '0.00 €') {
        alert('Zgjedh të paktën 1 copë për të kthyer!');
        return false;
    }
    return confirm('Do të kthehet pagesa prej ' + total + ' te klienti përmes Stripe. Jeni i sigurt?');
}

/* ═══════════════════════════════════════════════════════════
   === products/create.blade.php ===
   ═══════════════════════════════════════════════════════════ */

// ════════════ FOTO KRYESORE TË PRODUKTIT ════════════
let selectedFiles = [];
const existingCount = {{ isset($product) ? ($product->images->count() ?? 0) : 0 }};

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
        const exists = selectedFiles.some(s =>
            s.name === f.name && s.size === f.size && s.lastModified === f.lastModified
        );
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
            div.innerHTML = `
                <img src="${ev.target.result}" alt="${file.name}">
                ${i === 0 ? '<div class="p-label"><i class="fa-solid fa-star" style="font-size:8px;"></i> KRYESORE</div>' : ''}
                <div class="prev-actions">
                    <button type="button" class="prev-rm-btn" onclick="removeFile(${i})">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>`;
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
let variantIndex = {{ isset($product) && $product->variants
    ? $product->variants->groupBy(function ($v) {
        return trim(($v->color_hex ?? '')) . '||' . trim(($v->color_name ?? ''));
    })->count()
    : 0 }};

const storageCounters   = {};
const variantPhotoFiles = {};

function updateSwatch(vi, color) {
    const swatch = document.getElementById(`swatchPreview_${vi}`);
    if (swatch) swatch.style.background = color;
}

// ✅ FIX: GLOBAL scope — të thirrueshme nga oninput="syncBasePrice(...)" në HTML
function syncBasePrice(vi) {
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
        const hidden  = document.getElementById(`basePriceHidden_${vi}_${si}`);
        const display = document.getElementById(`baseDisplay_${vi}_${si}`);
        if (hidden)  hidden.value    = base;
        if (display) display.textContent = base > 0 ? base.toFixed(2) + ' €' : '—';
        calcTotal(vi, parseInt(si));
    });
}

// ✅ FIX: GLOBAL scope
function calcTotal(vi, si) {
    const base = parseFloat(document.getElementById(`basePrice_${vi}`)?.value || 0);
    const row  = document.getElementById(`storageRow_${vi}_${si}`);
    if (!row) return;
    const extra = parseFloat(row.querySelector('[name*="extra_price"]')?.value || 0);
    const el    = document.getElementById(`total_${vi}_${si}`);
    if (el) el.textContent = (base + extra) > 0 ? `${(base + extra).toFixed(2)} €` : '—';
}

// ✅ FIX: GLOBAL scope + argumentet e sakta
function refreshAllTotals(vi) {
    const container = document.getElementById(`storageRows_${vi}`);
    if (!container) return;
    container.querySelectorAll('.storage-row').forEach(row => {
        const extraInput = row.querySelector('[name*="extra_price"]');
        if (!extraInput) return;
        const match = extraInput.getAttribute('name').match(/storages\]\[(\d+)\]/);
        if (match) calcTotal(vi, parseInt(match[1])); // ✅ 2 args (jo 3)
    });
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
        const exists = variantPhotoFiles[vi].some(f =>
            f.name === file.name && f.size === file.size && f.lastModified === file.lastModified
        );
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
    if (item.querySelector('input[type="hidden"]')) {
        item.remove();
        refreshPrimaryBadges(vi);
        return;
    }
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
    row.innerHTML = `
        <select name="variants[${vi}][storages][${si}][storage]" class="form-select">
            <option value="">— Zgjedh —</option>
            <option value="64GB">64GB</option>
            <option value="128GB">128GB</option>
            <option value="256GB">256GB</option>
            <option value="512GB">512GB</option>
            <option value="1TB">1TB</option>
            <option value="2TB">2TB</option>
        </select>
        ${isFirst
            ? `<input type="number" step="0.01" name="variants[${vi}][storages][${si}][base_price]" class="form-control storage-base" id="basePrice_${vi}" placeholder="999.00" oninput="syncBasePrice(${vi}); calcTotal(${vi}, ${si})">`
            : `<div style="font-size:12px;color:var(--text-muted);align-self:center;padding:0 4px;"><i class="fa-solid fa-arrow-turn-down-right" style="font-size:10px;"></i><span id="baseDisplay_${vi}_${si}" style="font-weight:600;">—</span></div><input type="hidden" name="variants[${vi}][storages][${si}][base_price]" id="basePriceHidden_${vi}_${si}" value="0">`
        }
        <input type="number" step="0.01" name="variants[${vi}][storages][${si}][extra_price]" class="form-control" placeholder="+0.00" oninput="calcTotal(${vi}, ${si})">
        <div style="font-size:13px;color:#059669;font-weight:700;align-self:center;" id="total_${vi}_${si}">—</div>
        <input type="number" name="variants[${vi}][storages][${si}][stock]" class="form-control" value="0" placeholder="0">
        <button type="button" class="rm-btn-sm" onclick="removeStorageRow(this)"><i class="fa-solid fa-xmark"></i></button>`;
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
let specIndex = {{ isset($product) && $product->specs ? $product->specs->count() : 0 }};

function addSpec(keyName = '') {
    document.getElementById('specsEmpty')?.style.setProperty('display', 'none');
    const si  = specIndex++;
    const row = document.createElement('div');
    row.className = 'spec-row';
    row.id = `specRow_${si}`;
    row.innerHTML = `
        <input type="text" name="specs[${si}][key]" class="form-control" value="${keyName}" placeholder="p.sh. Ekrani">
        <input type="text" name="specs[${si}][value]" class="form-control" placeholder="p.sh. 6.7&quot; OLED">
        <button type="button" onclick="removeSpec(this)" class="rm-btn"><i class="fa-solid fa-xmark"></i></button>`;
    document.getElementById('specsList').appendChild(row);
    setTimeout(() => {
        const inputs = row.querySelectorAll('input');
        (keyName ? inputs[1] : inputs[0])?.focus();
    }, 50);
}

function removeSpec(button) {
    button.closest('.spec-row')?.remove();
    if (!document.querySelectorAll('#specsList .spec-row').length) {
        const empty = document.getElementById('specsEmpty');
        if (empty) empty.style.display = 'block';
    }
}

// ════════════ FOTO EKZISTUESE TË PRODUKTIT ════════════
function deleteImage(imgId, productId) {
    if (!confirm('Fshi këtë foto?')) return;
    fetch(`/admin/products/${productId}/images/${imgId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
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
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
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

// ════════════ DOMContentLoaded ════════════
document.addEventListener('DOMContentLoaded', function () {

    // Inicializo counter-at për variantet ekzistuese (edit mode)
    document.querySelectorAll('.variant-color-group').forEach(group => {
        const vi = parseInt((group.id || '').split('_')[1], 10);
        if (isNaN(vi)) return;
        const container = document.getElementById(`storageRows_${vi}`);
        storageCounters[vi] = container
            ? container.querySelectorAll('.storage-row').length
            : group.querySelectorAll('.storage-row').length;
        variantPhotoFiles[vi] = [];
    });

    // ── CATEGORY → BRAND ──
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
            const currentBrandId = {{ isset($product) && $product->brand_id ? $product->brand_id : 'null' }};

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

            // Nënkategoritë
            const subcatGroup  = document.getElementById('subcategoryGroup');
            const subcatSelect = document.getElementById('subcategorySelect');
            if (subcatGroup && subcatSelect) {
                subcatSelect.innerHTML = '<option value="">— Zgjedh nënkategorinë —</option>';
                subcatGroup.style.display = 'none';
                if (categoryId) {
                    fetch(`/admin/categories/${categoryId}/subcategories`)
                        .then(r => r.json())
                        .then(subs => {
                            if (!subs.length) return;
                            const currentSub = '{{ $product->subcategory ?? '' }}';
                            subs.forEach(name => {
                                const opt = document.createElement('option');
                                opt.value = name;
                                opt.textContent = name;
                                if (name === currentSub) opt.selected = true;
                                subcatSelect.appendChild(opt);
                            });
                            subcatGroup.style.display = '';
                        })
                        .catch(() => {});
                }
            }

            toggleLinkedCard();
        });

        if (categorySelect.value) categorySelect.dispatchEvent(new Event('change'));
    }

    toggleLinkedCard();

    // Linked products filter
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

    // ── Slug & Icon (categories) ──
    const slugField = document.getElementById('slugField');
    if (slugField) {
        slugField.addEventListener('input', function () {
            this.dataset.edited = 'true';
            const preview = document.getElementById('slugPreview');
            if (preview) preview.textContent = this.value || '...';
        });
        const nameInput = document.querySelector('[name="name"]');
        if (nameInput) generateSlug(nameInput.value);
    }

    const iconInput = document.getElementById('iconInput');
    if (iconInput) {
        previewIcon(iconInput.value);
        iconInput.addEventListener('paste', function (e) {
            e.preventDefault();
            let pasted = (e.clipboardData || window.clipboardData).getData('text').trim();
            let name = pasted;
            if (pasted.includes('fontawesome.com/icons/')) {
                const m = pasted.match(/icons\/([a-z0-9-]+)/);
                if (m) name = m[1];
            } else if (pasted.includes('icons.getbootstrap.com/icons/')) {
                const m = pasted.match(/icons\/([a-z0-9-]+)/);
                if (m) name = m[1];
            } else if (pasted.startsWith('http')) {
                const parts = pasted.replace(/\/$/, '').split('/');
                name = parts[parts.length - 1];
            }
            name = name.replace(/^fa-/, '').replace(/^bi-/, '').replace(/[?#].*$/, '');
            this.value = name;
            previewIcon(name);
        });
    }

    // ── Products index ──
    const selectAll = document.getElementById('selectAll') || document.getElementById('select-all');
    if (selectAll) {
        selectAll.addEventListener('change', function () {
            document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
            if (typeof updateBulkDeleteButton === 'function') updateBulkDeleteButton();
        });
    }

    document.querySelectorAll('.row-check').forEach(ch => {
        ch.addEventListener('change', function () {
            const all     = document.querySelectorAll('.row-check');
            const checked = document.querySelectorAll('.row-check:checked');
            if (selectAll) {
                selectAll.checked = all.length > 0 && all.length === checked.length;
            }
            if (typeof updateBulkDeleteButton === 'function') updateBulkDeleteButton();
        });
    });

    // ── Responsive products table ──
    function applyResponsive() {
        const isMobile   = window.innerWidth <= 768;
        const tableWrap  = document.querySelector('.table-wrap');
        const mobileList = document.querySelector('.mobile-list');
        if (!tableWrap || !mobileList) return;
        tableWrap.style.display  = isMobile ? 'none' : '';
        mobileList.style.display = isMobile ? 'block' : 'none';
    }
    applyResponsive();
    window.addEventListener('resize', applyResponsive);

    // ── calcTotal init për storage rows ekzistuese ──
    document.querySelectorAll('.variant-color-group').forEach(group => {
        const vi = parseInt((group.id || '').split('_')[1], 10);
        if (isNaN(vi)) return;
        refreshAllTotals(vi);
    });
});

/* === products/index.blade.php === */
function updateBulkDeleteButton() {
    const checked = document.querySelectorAll('.row-check:checked');
    const btn = document.getElementById('bulkDeleteBtn');
    if (!btn) return;
    if (checked.length > 0) {
        btn.style.display = 'inline-flex';
        btn.innerHTML = `<i class="fa-solid fa-trash"></i> Fshi të zgjedhurat (${checked.length})`;
    } else {
        btn.style.display = 'none';
    }
}

function submitBulkDelete() {
    const checked = document.querySelectorAll('.row-check:checked');
    if (!checked.length) return;
    if (!confirm('A je i sigurt që dëshiron t\'i fshish produktet e zgjedhura?')) return;
    const container = document.getElementById('selectedProductsContainer');
    const form      = document.getElementById('bulkDeleteForm');
    container.innerHTML = '';
    checked.forEach(ch => {
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = 'product_ids[]';
        input.value = ch.value;
        container.appendChild(input);
    });
    form.submit();
}

// calcTotal është gjithashtu përdorur nga orders
// (orders/create e thirr direkt — ky alias siguron kompatibilitetin)
if (typeof calcTotal === 'undefined') {
    function calcTotal() {}
}