/* resources/js/admin/products-index.js */

// Search filter — works on desktop table AND mobile list
function filterTable() {
    const q        = (document.getElementById('searchInput')?.value || '').toLowerCase();
    const category = (document.getElementById('filterCategory')?.value || '').toLowerCase();
    const brand    = (document.getElementById('filterBrand')?.value || '').toLowerCase();
    const status   = (document.getElementById('filterStatus')?.value || '').toLowerCase();

    document.querySelectorAll('#productsTable tbody tr').forEach(row => {
        const text = row.textContent.toLowerCase();
        const matchQ        = !q        || text.includes(q);
        const matchCategory = !category || text.includes(category);
        const matchBrand    = !brand    || text.includes(brand);
        const matchStatus   = !status   || (row.dataset.status ?? '') === status; // 👈
        row.style.display = (matchQ && matchCategory && matchBrand && matchStatus) ? '' : 'none';
    });

    document.querySelectorAll('.mobile-product-card').forEach(card => {
        const text = card.textContent.toLowerCase();
        const matchQ        = !q        || text.includes(q);
        const matchCategory = !category || text.includes(category);
        const matchBrand    = !brand    || text.includes(brand);
        const matchStatus   = !status   || (card.dataset.status ?? '') === status; // 👈
        card.style.display = (matchQ && matchCategory && matchBrand && matchStatus) ? '' : 'none';
    });
}

    // Responsive toggle — JS backup in case CSS media query fails
    // (e.g. missing viewport meta in parent layout)
function applyResponsive() {
    const isMobile = window.innerWidth <= 768;
    const tableWrap = document.querySelector('.table-wrap');
    const mobileList = document.querySelector('.mobile-list');
    const mobileSelectBar = document.getElementById('mobileSelectBar');
    if (!tableWrap || !mobileList) return;
    if (isMobile) {
        tableWrap.style.display = 'none';
        mobileList.style.display = 'block';
        if (mobileSelectBar) mobileSelectBar.style.display = 'flex';
    } else {
        tableWrap.style.display = '';
        mobileList.style.display = 'none';
        if (mobileSelectBar) mobileSelectBar.style.display = 'none';
    }
}

    // Run on load and on resize
    applyResponsive();
    window.addEventListener('resize', applyResponsive);
function updateBulkDeleteButton() {
    // Numëro vetëm të dukshmet
    let count = 0;
    document.querySelectorAll('.row-check:checked').forEach(ch => {
        const row  = ch.closest('tr');
        const card = ch.closest('.mobile-product-card');
        if (row  && row.style.display  !== 'none') count++;
        if (card && card.style.display !== 'none') count++;
    });

    const btn = document.getElementById('bulkDeleteBtn');
    if (!btn) return;

    if (count > 0) {
        btn.style.display = 'inline-flex';
        btn.innerHTML = `<i class="fa-solid fa-trash"></i> Fshi të zgjedhurat (${count})`;
    } else {
        btn.style.display = 'none';
    }
}
  document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAll');

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            // Selekto vetëm të dukshmet
            document.querySelectorAll('#productsTable tbody tr').forEach(row => {
                if (row.style.display === 'none') return;
                const check = row.querySelector('.row-check');
                if (check) check.checked = selectAll.checked;
            });
            document.querySelectorAll('.mobile-product-card').forEach(card => {
                if (card.style.display === 'none') return;
                const check = card.querySelector('.row-check');
                if (check) check.checked = selectAll.checked;
            });
            updateBulkDeleteButton();
        });
    }

    document.querySelectorAll('.row-check').forEach(ch => {
        ch.addEventListener('change', function () {
            updateBulkDeleteButton();
        });
    });
});
function submitBulkDelete() {
    const visible = [];
    document.querySelectorAll('.row-check:checked').forEach(ch => {
        const row  = ch.closest('tr');
        const card = ch.closest('.mobile-product-card');
        if ((row  && row.style.display  !== 'none') ||
            (card && card.style.display !== 'none')) {
            visible.push(ch);
        }
    });

    if (!visible.length) return;
    if (!confirm(`A je i sigurt që dëshiron t'i fshish ${visible.length} produktet e zgjedhura?`)) return;

    const container = document.getElementById('selectedProductsContainer');
    const form = document.getElementById('bulkDeleteForm');
    container.innerHTML = '';
    visible.forEach(ch => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'product_ids[]';
        input.value = ch.value;
        container.appendChild(input);
    });
    form.submit();
}

