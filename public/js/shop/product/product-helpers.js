(function () {
    const data = window.productDetailData || {};

    const ProductData = {
        colorGroups: data.colorGroups || [],
        PRODUCT_ID: data.productId || null,
        BASE_PRICE: Number(data.basePrice || 0),
        BASE_OLD: data.baseOld != null ? Number(data.baseOld) : null,
        BASE_STOCK: Number(data.baseStock || 0),
        productImages: Array.isArray(data.productImages) ? data.productImages : [],
        colorImages: Array.isArray(data.colorImages) ? data.colorImages : []
    };

    ProductData.allProductSrcs = [...ProductData.productImages, ...ProductData.colorImages];

    function getGroup(colorName) {
        if (!colorName) return null;
        return ProductData.colorGroups.find(g => g.color_name === colorName) || null;
    }

    function getStorage(group, variantId) {
        if (!group || !variantId) return null;
        return (group.storages || []).find(s => String(s.id) === String(variantId)) || null;
    }

    function setOrderButtonDefault() {
        const orderBtn = document.getElementById('orderBtn');
        const orderBtnText = document.getElementById('orderBtnText');

        if (orderBtn) orderBtn.classList.remove('success');
        if (orderBtnText) orderBtnText.textContent = 'Shto në shportë';
    }

    function clearThumbs() {
        document.querySelectorAll('#colorGallery .pm-thumb, #productThumbs .pm-thumb')
            .forEach(btn => btn.classList.remove('active'));
    }

    window.ProductData = ProductData;
    window.ProductHelpers = {
        getGroup,
        getStorage,
        setOrderButtonDefault,
        clearThumbs
    };
})();function toggleDrop(btn) {
    const wrap = btn.closest('.brand-dd-wrap');
    const isOpen = wrap.classList.contains('open');
    
    // Mbyll të gjitha dropdown-et e tjera
    document.querySelectorAll('.brand-dd-wrap.open').forEach(w => w.classList.remove('open'));
    
    // Hap/mbyll këtë
    if (!isOpen) wrap.classList.add('open');
}

// Mbyll kur klikon jashtë
document.addEventListener('click', function() {
    document.querySelectorAll('.brand-dd-wrap.open').forEach(w => w.classList.remove('open'));
});