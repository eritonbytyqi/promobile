(function () {
    const state = window.ProductDetailState;
    const data = window.ProductData;
    const helpers = window.ProductHelpers;

    function updatePopupCart(dataResponse) {
        if (dataResponse.cart_html && document.getElementById('csBody')) {
            document.getElementById('csBody').innerHTML = dataResponse.cart_html;
        }

        if (typeof dataResponse.cart_count !== 'undefined') {
            const badge = document.getElementById('csCountBadge');
            if (badge) {
                badge.textContent = dataResponse.cart_count;
                badge.style.display = dataResponse.cart_count > 0 ? '' : 'none';
            }
        }

        if (typeof dataResponse.cart_total !== 'undefined') {
            const total = document.getElementById('csTotal');
            if (total) total.textContent = dataResponse.cart_total + ' €';
        }
    }

    function closeCartPopup() {
        document.getElementById('cartPopup')?.classList.remove('open');
        document.body.style.overflow = '';
    }

    function addCurrentProductToCart(button) {
        if (!button || button.disabled) return;

        const group = helpers.getGroup(state.selColorName);
        const storage = helpers.getStorage(group, state.selVariantId);

        const payload = {
            product_id: data.PRODUCT_ID,
            quantity: 1
        };

        if (storage) {
            payload.variant_id = storage.id;
        }

        fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(result => {
            if (!result.success) return;

            document.getElementById('cartPopup')?.classList.add('open');
            document.body.style.overflow = 'hidden';

            button.classList.add('success');

            const orderBtnText = document.getElementById('orderBtnText');
            if (orderBtnText) orderBtnText.textContent = 'U shtua ✓';

            if (typeof openCart === 'function') openCart();

            updatePopupCart(result);
        })
        .catch(err => console.error('Cart add error:', err));
    }

    function initCartPopup() {
        document.getElementById('cartPopup')?.addEventListener('click', function (e) {
            if (e.target === this) {
                closeCartPopup();
            }
        });
    }

    window.ProductCart = {
        addCurrentProductToCart,
        closeCartPopup,
        initCartPopup
    };

    window.addCurrentProductToCart = addCurrentProductToCart;
    window.closeCartPopup = closeCartPopup;
})();