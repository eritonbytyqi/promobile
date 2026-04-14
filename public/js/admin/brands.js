/* resources/js/admin/brands.js */

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
