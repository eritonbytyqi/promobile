/* resources/js/admin/orders-index.js */

function filterTable() {
    const q  = document.getElementById('searchInput').value.toLowerCase();
    const st = document.getElementById('statusFilter').value;
    document.querySelectorAll('#ordersTable tbody tr').forEach(row => {
        const matchText   = !q  || row.textContent.toLowerCase().includes(q);
        const matchStatus = !st || (row.dataset.status??'') === st;
        row.style.display = (matchText && matchStatus) ? '' : 'none';
    });
    document.querySelectorAll('.order-mobile-card').forEach(card => {
        const matchText   = !q  || card.textContent.toLowerCase().includes(q);
        const matchStatus = !st || (card.dataset.status??'') === st;
        card.style.display = (matchText && matchStatus) ? '' : 'none';
    });
}

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
