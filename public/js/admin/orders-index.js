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
    // Desktop — vetëm rreshtat e dukshëm
    document.querySelectorAll('#ordersTable tbody tr').forEach(row => {
        if (row.style.display === 'none') return;
        const check = row.querySelector('.row-check');
        if (check) { check.checked = cb.checked; row.classList.toggle('selected', cb.checked); }
    });
    // Mobile — vetëm kartat e dukshme
    document.querySelectorAll('.order-mobile-card').forEach(card => {
        if (card.style.display === 'none') return;
        const check = card.querySelector('.row-check');
        if (check) check.checked = cb.checked;
    });
    updateBulkBar();
}

function updateBulkBar() {
    // Numëro vetëm të dukshmet
    let count = 0;
    const container = document.getElementById('bulkInputs');
    container.innerHTML = '';

    document.querySelectorAll('.row-check:checked').forEach(c => {
        const row  = c.closest('tr');
        const card = c.closest('.order-mobile-card');
        const visible = (row && row.style.display !== 'none') ||
                        (card && card.style.display !== 'none');
        if (!visible) return;
        count++;
        const inp = document.createElement('input');
        inp.type='hidden'; inp.name='ids[]'; inp.value=c.value;
        container.appendChild(inp);
    });

    document.getElementById('bulkCount').textContent = count;
    document.getElementById('bulkBar').classList.toggle('show', count > 0);

    const sel = document.getElementById('selectAll');
    if (sel) {
        const allVisible = document.querySelectorAll('#ordersTable tbody tr:not([style*="display: none"]) .row-check').length;
        sel.indeterminate = count > 0 && count < allVisible;
        sel.checked = allVisible > 0 && count === allVisible;
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
    const count = parseInt(document.getElementById('bulkCount').textContent) || 0;
    return confirm('A jeni i sigurt? Do të fshihen ' + count + ' porosi permanentisht!');
}   
// Responsive
function applyResponsive() {
    const isMobile = window.innerWidth <= 768;
    const tableWrap = document.querySelector('.table-wrap');
    const mobileOrders = document.querySelector('.mobile-orders');
    const mobileBar = document.getElementById('ordersMobileSelectBar');
    if (!tableWrap || !mobileOrders) return;
    if (isMobile) {
        tableWrap.style.display = 'none';
        mobileOrders.style.display = 'block';
        if (mobileBar) mobileBar.style.display = 'flex';
    } else {
        tableWrap.style.display = '';
        mobileOrders.style.display = 'none';
        if (mobileBar) mobileBar.style.display = 'none';
    }
}

applyResponsive();
window.addEventListener('resize', applyResponsive);

// Select all mobile
document.addEventListener('DOMContentLoaded', function () {
    const selectAllMobile = document.getElementById('selectAllMobile');
    if (selectAllMobile) {
        selectAllMobile.addEventListener('change', function () {
            document.querySelectorAll('.order-mobile-card').forEach(card => {
                if (card.style.display === 'none') return;
                const check = card.querySelector('.row-check');
                if (check) check.checked = selectAllMobile.checked;
            });
            updateBulkBar();
        });
    }
});