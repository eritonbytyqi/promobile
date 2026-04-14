/* resources/js/admin/orders-create.js */

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

function calcTotal() {
    let sub = 0;
    document.querySelectorAll('.order-item-row').forEach(row => {
        const qty   = parseFloat(row.querySelector('.item-qty')?.value) || 0;
        const price = parseFloat(row.querySelector('.item-price')?.value) || 0;
        sub += qty * price;
    });
    const total = sub + 2.50;
    document.getElementById('subtotalDisplay').textContent = sub.toFixed(2) + ' €';
    document.getElementById('totalDisplay').textContent = total.toFixed(2) + ' €';
    document.getElementById('totalReadout').textContent = total.toFixed(2) + ' €';
    document.getElementById('totalInput').value = total.toFixed(2);
}

calcTotal();
