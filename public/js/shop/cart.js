function addToCart(id, btn, variantId = null) {
    const icon = btn.querySelector('i');
    if (icon) icon.className = 'fa-solid fa-spinner fa-spin';

    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            product_id: id,
            quantity: 1,
            variant_id: variantId
        })
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) return;

        refreshCartUI(data);
        openCartUIOnly();

        if (icon) icon.className = 'fa-solid fa-check';
    })
    .catch(() => {
        if (icon) icon.className = 'fa-solid fa-plus';
    });
}