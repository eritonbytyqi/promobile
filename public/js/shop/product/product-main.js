(function () {
    const scripts = [
        '/js/shop/product/product-state.js',
        '/js/shop/product/product-gallery.js',
        '/js/shop/product/product-helpers.js',
        '/js/shop/product/product-variants.js',
        '/js/shop/product/product-cart.js',
        '/js/shop/product/product-wishlist.js',
        '/js/shop/product/product-init.js',
        '/js/shop/product/product-state.js',
        '/js/shop/pages.js'
    ];

    function loadScriptSequentially(index) {
        if (index >= scripts.length) return;

        const script = document.createElement('script');
        script.src = scripts[index];
        script.onload = function () {
            loadScriptSequentially(index + 1);
        };
        script.onerror = function () {
            console.error('Nuk u gjet script:', scripts[index]);
        };

        document.head.appendChild(script);
    }

    loadScriptSequentially(0);
})();