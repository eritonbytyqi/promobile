/* resources/js/admin/settings.js */

/* ── Quill editors ── */
const toolbarOpts = [
    ['bold','italic','underline'],
    [{'header':2},{'header':3}],
    [{'list':'ordered'},{'list':'bullet'}],
    ['link'],['clean']
];

const termsQuill = new Quill('#terms_editor', {
    theme: 'snow',
    placeholder: 'Shkruaj kushtet e përdorimit këtu...',
    modules: { toolbar: toolbarOpts }
});

const privacyQuill = new Quill('#privacy_editor', {
    theme: 'snow',
    placeholder: 'Shkruaj politikën e privatësisë këtu...',
    modules: { toolbar: toolbarOpts }
});

const termsContent   = @json($settings['terms_content']   ?? '');
const privacyContent = @json($settings['privacy_content'] ?? '');
if (termsContent)   termsQuill.root.innerHTML   = termsContent;
if (privacyContent) privacyQuill.root.innerHTML = privacyContent;

document.querySelector('#panel-terms form').addEventListener('submit', function () {
    this.querySelector('textarea[name="terms_content"]').value = termsQuill.root.innerHTML;
});
document.querySelector('#panel-privacy form').addEventListener('submit', function () {
    this.querySelector('textarea[name="privacy_content"]').value = privacyQuill.root.innerHTML;
});

/* ── Tab switching ── */
function swTab(id, el) {
    document.querySelectorAll('.sw-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.sw-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('panel-' + id).classList.add('active');
    el.classList.add('active');
}

/* ── Locations ── */
let locIdx = document.querySelectorAll('.loc-card').length;

function addLoc() {
    const wrap = document.getElementById('locationsWrap');
    const num  = wrap.querySelectorAll('.loc-card').length + 1;
    const i    = locIdx++;

    const html = `
    <div class="loc-card" data-index="${i}">
        <div class="loc-head">
            <div class="loc-label">
                <span>Lokacioni</span>
                <span class="loc-badge loc-number">${num}</span>
            </div>
            <button type="button" class="btn-remove" onclick="removeLoc(this)">
                 Largo
            </button>
        </div>
        <div class="sw-grid2 mb12">
            <div class="sw-field">
                <label>Emri / Qyteti</label>
                <input type="text" name="locations[${i}][name]" placeholder="Prishtinë">
            </div>
            <div class="sw-field">
                <label>Telefoni</label>
                <input type="text" name="locations[${i}][phone]" placeholder="+383 44 000 000">
            </div>
        </div>
        <div class="sw-field mb10">
            <label>Adresa e Plotë</label>
            <input type="text" class="loc-addr-full"
                   name="locations[${i}][address_full]"
                   placeholder="Rr. Nënë Tereza, Nr.15, Prishtinë"
                   oninput="updateLocMap(this)">
        </div>
        <div class="sw-field">
            <label>Adresa e Shkurtër</label>
            <input type="text" name="locations[${i}][address_short]" placeholder="Prishtinë, Kosovë">
        </div>
        <div class="map-preview loc-map-wrap" style="display:none">
            <iframe class="loc-map-iframe" loading="lazy"></iframe>
        </div>
    </div>`;

    wrap.insertAdjacentHTML('beforeend', html);
    refreshLocNums();
}

function removeLoc(btn) {
    const wrap  = document.getElementById('locationsWrap');
    const cards = wrap.querySelectorAll('.loc-card');
    if (cards.length <= 1) { return; }
    btn.closest('.loc-card').remove();
    reindexLocs();
    refreshLocNums();
}

function refreshLocNums() {
    document.querySelectorAll('#locationsWrap .loc-card').forEach((c, i) => {
        const el = c.querySelector('.loc-number');
        if (el) el.textContent = i + 1;
    });
}

function reindexLocs() {
    document.querySelectorAll('#locationsWrap .loc-card').forEach((card, i) => {
        card.setAttribute('data-index', i);
        card.querySelectorAll('input').forEach(inp => {
            const n = inp.getAttribute('name');
            if (n) inp.setAttribute('name', n.replace(/locations\[\d+\]/, `locations[${i}]`));
        });
    });
}

function updateLocMap(input) {
    const card   = input.closest('.loc-card');
    const wrap   = card.querySelector('.loc-map-wrap');
    const iframe = card.querySelector('.loc-map-iframe');
    const val    = input.value.trim();

    clearTimeout(input._mapTimer);

    if (!val) { wrap.style.display = 'none'; iframe.src = ''; return; }

    input._mapTimer = setTimeout(() => {
        iframe.src = 'https://maps.google.com/maps?q=' + encodeURIComponent(val) + '&output=embed';
        wrap.style.display = 'block';
    }, 700);
}
