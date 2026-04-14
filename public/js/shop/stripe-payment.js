document.addEventListener('DOMContentLoaded', () => {
    if (typeof Stripe === 'undefined') {
        console.error('Stripe.js nuk u ngarkua');
        return;
    }

    if (!window.stripeKey) {
        console.error('stripeKey mungon');
        return;
    }

    const stripe = Stripe(window.stripeKey);
    const elements = stripe.elements();

    const cardElement = elements.create('card', {
        style: {
            base: {
                fontSize: '15px',
                color: '#1a1c1d',
                fontFamily: 'Inter, sans-serif',
                '::placeholder': {
                    color: '#717785'
                }
            },
            invalid: {
                color: '#ba1a1a'
            }
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
        const btn = document.getElementById('payBtn');
        const spinner = document.getElementById('paySpinner');
        const btnText = document.getElementById('payBtnText');

        if (!btn || !spinner || !btnText) return;

        btn.disabled = true;
        spinner.style.display = 'block';
        btnText.textContent = 'Duke procesuar...';

        try {
            const intentRes = await fetch('/payment/intent', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    order_id: window.orderId
                })
            });

            const intentData = await intentRes.json();
            const clientSecret = intentData.client_secret;

            const result = await stripe.confirmCardPayment(clientSecret, {
                payment_method: {
                    card: cardElement
                }
            });

            if (result.error) {
                document.getElementById('card-errors').innerHTML =
                    '<span class="material-symbols-outlined" style="font-size:14px;">error</span>' + result.error.message;
                btn.disabled = false;
                spinner.style.display = 'none';
                btnText.textContent = 'Paguaj ' + window.orderTotal + ' €';
                return;
            }

            const paymentIntent = result.paymentIntent;

            const confirmRes = await fetch('/payment/confirm', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    order_id: window.orderId,
                    payment_intent_id: paymentIntent.id
                })
            });

            const confirmData = await confirmRes.json();

            if (confirmData.success) {
                window.location.href = window.orderSuccessUrl;
                return;
            }

            btn.disabled = false;
            spinner.style.display = 'none';
            btnText.textContent = 'Paguaj ' + window.orderTotal + ' €';
        } catch (err) {
            console.error(err);
            btn.disabled = false;
            spinner.style.display = 'none';
            btnText.textContent = 'Paguaj ' + window.orderTotal + ' €';
        }
    };
});
