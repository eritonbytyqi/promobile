/* resources/js/admin/banners.js */

function previewBannerImage(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = ev => {
        const bg = document.getElementById('previewBg');
        if (bg) {
            bg.style.backgroundImage = `url('${ev.target.result}')`;
            document.getElementById('noImgHint').style.display = 'none';
        }
    };
    reader.readAsDataURL(input.files[0]);
}

function setSwatchColor(hex, el) {
    document.getElementById('bgColorPicker').value = hex;
    document.getElementById('bgColorText').value   = hex;
    document.getElementById('bannerPreview').style.backgroundColor = hex;
    document.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('active'));
    el.classList.add('active');
}

function setBadge(txt) {
    const input = document.querySelector('input[name="banner_badge"]');
    if (input) input.value = txt;
    document.getElementById('previewBadge').textContent = txt;
}
function previewBannerVideo(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = ev => {
        const video = document.getElementById('previewVideo');
        const bg    = document.getElementById('previewBg');
        if (video) {
            video.src = ev.target.result;
            video.style.display = 'block';
            // Fshih foton kur ka video
            if (bg) bg.style.backgroundImage = '';
            document.getElementById('noImgHint').style.display = 'none';
        }
    };
    reader.readAsDataURL(input.files[0]);
}