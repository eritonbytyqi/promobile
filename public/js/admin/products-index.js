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
        const matchStatus   = !status   || text.includes(status);
        row.style.display = (matchQ && matchCategory && matchBrand && matchStatus) ? '' : 'none';
    });

    document.querySelectorAll('.mobile-product-card').forEach(card => {
        const text = card.textContent.toLowerCase();
        const matchQ        = !q        || text.includes(q);
        const matchCategory = !category || text.includes(category);
        const matchBrand    = !brand    || text.includes(brand);
        const matchStatus   = !status   || text.includes(status);
        card.style.display = (matchQ && matchCategory && matchBrand && matchStatus) ? '' : 'none';
    });
}

    // Responsive toggle — JS backup in case CSS media query fails
    // (e.g. missing viewport meta in parent layout)
    function applyResponsive() {
        const isMobile = window.innerWidth <= 768;
        const tableWrap = document.querySelector('.table-wrap');
        const mobileList = document.querySelector('.mobile-list');
        if (!tableWrap || !mobileList) return;
        if (isMobile) {
            tableWrap.style.display = 'none';
            mobileList.style.display = 'block';
        } else {
            tableWrap.style.display = '';
            mobileList.style.display = 'none';
        }
    }

    // Run on load and on resize
    applyResponsive();
    window.addEventListener('resize', applyResponsive);
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

    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('selectAll');
        const rowChecks = document.querySelectorAll('.row-check');

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                rowChecks.forEach(ch => ch.checked = selectAll.checked);
                updateBulkDeleteButton();
            });
        }

        rowChecks.forEach(ch => {
            ch.addEventListener('change', function () {
                const all = document.querySelectorAll('.row-check');
                const checked = document.querySelectorAll('.row-check:checked');

                if (selectAll) {
                    selectAll.checked = all.length > 0 && all.length === checked.length;
                }

                updateBulkDeleteButton();
            });
        });
    });

    function submitBulkDelete() {
        const checked = document.querySelectorAll('.row-check:checked');
        if (!checked.length) return;

        if (!confirm('A je i sigurt që dëshiron t’i fshish produktet e zgjedhura?')) {
            return;
        }

        const container = document.getElementById('selectedProductsContainer');
        const form = document.getElementById('bulkDeleteForm');

        container.innerHTML = '';

        checked.forEach(ch => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'product_ids[]';
            input.value = ch.value;
            container.appendChild(input);
        });

        form.submit();
    }
    const selectAll = document.getElementById('select-all');

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            document.querySelectorAll('.row-check').forEach(cb => {
                cb.checked = this.checked;
            });
        });
    }
