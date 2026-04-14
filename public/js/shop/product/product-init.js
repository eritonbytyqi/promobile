document.addEventListener('DOMContentLoaded', () => {
    const priceMain = document.getElementById('priceMain');
    if (priceMain) {
        priceMain.style.transition = 'opacity 0.15s';
    }

    if (window.ProductGallery) {
        window.ProductGallery.initGallery();
    }

    if (window.ProductCart) {
        window.ProductCart.initCartPopup();
    }

    if (window.ProductWishlist) {
        window.ProductWishlist.initWishlistButton();
    }

    if (window.ProductVariants) {
        window.ProductVariants.refreshUI();
    }
});