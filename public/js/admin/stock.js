/* resources/js/admin/stock.js */

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
