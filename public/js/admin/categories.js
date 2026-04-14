/* resources/js/admin/categories.js */

function filterTable() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('#catTable tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}

function generateSlug(val) {
    const slug = val.toLowerCase()
        .replace(/ë/g,'e').replace(/ç/g,'c')
        .replace(/[^a-z0-9\s-]/g,'')
        .trim().replace(/\s+/g,'-');
    const field = document.getElementById('slugField');
    if (!field.dataset.edited) field.value = slug;
    document.getElementById('slugPreview').textContent = slug || '...';
}

document.getElementById('slugField').addEventListener('input', function() {
    this.dataset.edited = 'true';
    document.getElementById('slugPreview').textContent = this.value || '...';
});

// Init slug preview
generateSlug(document.querySelector('[name="name"]').value);
function previewIcon(val) {
    val = val.trim().replace(/^fa-/, '');
    const full = 'fa-' + (val || 'tag');
    document.getElementById('iconPreviewI').className = 'fa-solid ' + full;
    document.getElementById('iconHidden').value = full;
}

// Init në load
document.addEventListener('DOMContentLoaded', () => {
    const inp = document.getElementById('iconInput');
    if (inp) previewIcon(inp.value);
});
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
document.getElementById('iconInput').addEventListener('paste', function(e) {
    e.preventDefault();
    var pasted = (e.clipboardData || window.clipboardData).getData('text').trim();
    var name = pasted;

    // FontAwesome
    if (pasted.includes('fontawesome.com/icons/')) {
        var match = pasted.match(/icons\/([a-z0-9-]+)/);
        if (match) name = match[1];
    }
    // Bootstrap Icons
    else if (pasted.includes('icons.getbootstrap.com/icons/')) {
        var match = pasted.match(/icons\/([a-z0-9-]+)/);
        if (match) name = match[1];
    }
    // Heroicons
    else if (pasted.includes('heroicons.com')) {
        var match = pasted.match(/\/([a-z0-9-]+)\/?$/);
        if (match) name = match[1];
    }
    // URL e përgjithshme — merr pjesën e fundit
    else if (pasted.startsWith('http')) {
        var parts = pasted.replace(/\/$/, '').split('/');
        name = parts[parts.length - 1];
    }

    // Hiq prefikset e njohura
    name = name.replace(/^fa-/, '')
               .replace(/^bi-/, '')
               .replace(/^icon-/, '')
               .replace(/[?#].*$/, ''); // hiq query params

    this.value = name;
    previewIcon(name);
});
function previewIcon(val) {
    val = val.trim().replace(/^fa-/, '');
    const full = 'fa-' + (val || 'tag');

    document.getElementById('iconPreviewI').className = 'fa-solid ' + full;
    document.getElementById('iconHidden').value = full;
}

document.addEventListener('DOMContentLoaded', () => {
    const inp = document.getElementById('iconInput');
    if (inp) previewIcon(inp.value);
});
function addSubcatRow(value = '') {
    const list = document.getElementById('subcatList');
    const row  = document.createElement('div');
    row.className = 'subcat-row';
    row.style.cssText = 'display:flex;gap:8px;align-items:center;';
    row.innerHTML = `
        <input type="text"
               name="subcategories[]"
               class="form-control"
               value="${value}"
               placeholder="p.sh. Kalikë">
        <button type="button"
                onclick="this.closest('.subcat-row').remove()"
                style="width:34px;height:34px;border-radius:8px;background:rgba(230,57,70,0.1);border:1px solid rgba(230,57,70,0.2);color:var(--danger);cursor:pointer;flex-shrink:0;display:flex;align-items:center;justify-content:center;">
            <i class="fa-solid fa-xmark" style="font-size:12px;"></i>
        </button>
    `;
    list.appendChild(row);
    row.querySelector('input').focus();
}