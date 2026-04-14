/* resources/js/admin/orders-show.js */

function updateRefundTotal() {
    let total = 0;
    document.querySelectorAll('input[name^="items"][name$="[quantity]"]').forEach(input => {
        const qty = parseInt(input.value) || 0;
        const priceInput = input.closest('div.card-body div').querySelector('input[name$="[unit_price]"]');
        // gjej unit_price nga forma
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
