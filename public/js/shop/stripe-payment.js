document.addEventListener('DOMContentLoaded', () => {
    if (typeof Stripe === 'undefined') {
        console.error('Stripe.js nuk u ngarkua');
        return;
    }

    if (!window.stripeKey) {
        console.error('stripeKey mungon');
        return;
    }

    const stripe   = Stripe(window.stripeKey);
    const elements = stripe.elements();

    const cardElement = elements.create('card', {
        style: {
            base: {
                fontSize: '15px',
                color: '#1a1c1d',
                fontFamily: 'Inter, sans-serif',
                '::placeholder': { color: '#717785' }
            },
            invalid: { color: '#ba1a1a' }
        }
    });

    cardElement.mount('#card-element');

    cardElement.on('change', function (e) {
        const errorDiv = document.getElementById('card-errors');
        if (!errorDiv) return;
        errorDiv.innerHTML = e.error
            ? '<span class="material-symbols-outlined" style="font-size:14px;">error</span>' + e.error.message
            : '';
    });

    window.handlePayment = async function () {
        const btn     = document.getElementById('payBtn');
        const spinner = document.getElementById('paySpinner');
        const btnText = document.getElementById('payBtnText');

        if (!btn || !spinner || !btnText) return;

        btn.disabled = true;
        spinner.style.display = 'block';
        btnText.textContent = 'Duke procesuar...';

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        try {
            // ── HAPI 1: Krijo PaymentIntent nga sesioni (pa order_id) ──
            const intentRes = await fetch('/payment/intent', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({})  // ✅ nuk ka order_id
            });

            if (!intentRes.ok) {
                const err = await intentRes.json();
                throw new Error(err.error ?? 'Gabim gjatë krijimit të pagesës');
            }

            const intentData   = await intentRes.json();
            const clientSecret = intentData.client_secret;

            // ── HAPI 2: Konfirmo kartën me Stripe ──
            const result = await stripe.confirmCardPayment(clientSecret, {
                payment_method: { card: cardElement }
            });

            if (result.error) {
                showError(result.error.message, btn, spinner, btnText);
                return;
            }

            // ── HAPI 3: Konfirmo te backend — krijo orderin ──
            const confirmRes = await fetch('/bank/confirm', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    status:             'confirmed',
                    payment_intent_id:  result.paymentIntent.id
                })
            });

            const confirmData = await confirmRes.json();

            if (confirmData.success && confirmData.redirect) {
                window.location.href = confirmData.redirect;
                return;
            }

            throw new Error(confirmData.error ?? 'Konfirmimi dështoi');

        } catch (err) {
            console.error(err);
            showError(err.message, btn, spinner, btnText);
        }
    };

    function showError(message, btn, spinner, btnText) {
        const errorDiv = document.getElementById('card-errors');
        if (errorDiv) {
            errorDiv.innerHTML =
                '<span class="material-symbols-outlined" style="font-size:14px;">error</span> ' + message;
        }
        btn.disabled          = false;
        spinner.style.display = 'none';
        btnText.textContent   = 'Paguaj ' + (window.orderTotalFormatted ?? '') + ' €';
    }
});