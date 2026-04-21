{{-- MODAL EMAIL STATUS --}}
<div id="statusModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;padding:28px;max-width:560px;width:90%;max-height:90vh;overflow-y:auto;">
        
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
            <h3 style="margin:0;font-size:17px;font-weight:700;color:#1d1d1f;">Ndrysho Statusin</h3>
            <button onclick="closeStatusModal()" style="background:none;border:none;font-size:20px;cursor:pointer;color:#8e8e93;">✕</button>
        </div>

        <div style="margin-bottom:16px;">
            <label style="font-size:12px;font-weight:600;color:#8e8e93;display:block;margin-bottom:6px;">STATUSI I RI</label>
            <select id="modalStatus" class="form-select" style="width:100%;">
                <option value="pending">Në Pritje</option>
                <option value="awaiting_payment">Pret Pagesën</option>
                <option value="confirmed">Konfirmuar</option>
                <option value="shipped">Dërguar</option>
                <option value="delivered">Dorëzuar</option>
                <option value="cancelled">Anuluar</option>
            </select>
        </div>

        <div style="background:#f5f5f7;border-radius:10px;padding:14px;margin-bottom:16px;">
            <label style="font-size:12px;font-weight:600;color:#8e8e93;display:block;margin-bottom:8px;">
                📧 EMAILI QË DO T'I DËRGOHET KLIENTIT
            </label>
            <div style="margin-bottom:8px;">
                <label style="font-size:11px;color:#8e8e93;">Subjekti:</label>
                <input type="text" id="modalEmailSubject" class="form-control" style="margin-top:4px;">
            </div>
            <div>
                <label style="font-size:11px;color:#8e8e93;">Mesazhi:</label>
                <textarea id="modalEmailBody" class="form-control" rows="6" style="margin-top:4px;resize:vertical;"></textarea>
            </div>
        </div>

        <div style="display:flex;gap:10px;">
            <button onclick="submitStatus(true)" class="btn btn-primary" style="flex:1;justify-content:center;">
                <i class="fa-solid fa-envelope"></i> Ndrysho & Dërgo Email
            </button>
            <button onclick="submitStatus(false)" class="btn btn-secondary" style="flex:1;justify-content:center;">
                <i class="fa-solid fa-rotate"></i> Ndrysho pa Email
            </button>
        </div>
    </div>
</div>

<script>
let currentOrderUuid = null;
let currentCustomerName = null;
let currentOrderNumber = null;

const emailTemplates = {
    confirmed: {
        subject: 'Porosia juaj u konfirmua! ✅',
        body: `Përshëndetje {customer_name},\n\nPorosia juaj #{order_number} u konfirmua me sukses!\nDo t'ju kontaktojmë së shpejti për dorëzimin.\n\nFaleminderit,\nProMobile`
    },
    shipped: {
        subject: 'Porosia juaj është rrugës! 🚚',
        body: `Përshëndetje {customer_name},\n\nLajm i mirë! Porosia juaj #{order_number} u dërgua dhe është rrugës.\nDo të arrijë brenda 1-3 ditëve pune.\n\nFaleminderit,\nProMobile`
    },
    delivered: {
        subject: 'Porosia juaj u dorëzua! ✓',
        body: `Përshëndetje {customer_name},\n\nPorosia juaj #{order_number} u dorëzua me sukses!\nShpresojmë të jeni të kënaqur me blerjen.\n\nFaleminderit,\nProMobile`
    },
    cancelled: {
        subject: 'Porosia juaj u anulua',
        body: `Përshëndetje {customer_name},\n\nPorosia juaj #{order_number} u anulua.\nNëse keni pyetje, na kontaktoni.\n\nFaleminderit,\nProMobile`
    },
};

function openStatusModal(uuid, currentStatus, customerName, orderNumber) {
    currentOrderUuid = uuid;
    currentCustomerName = customerName;
    currentOrderNumber = orderNumber;

    const modal = document.getElementById('statusModal');
    const statusSelect = document.getElementById('modalStatus');

    statusSelect.value = currentStatus;
    modal.style.display = 'flex';

    updateEmailTemplate(currentStatus);

    statusSelect.onchange = function() {
        updateEmailTemplate(this.value);
    };
}

function updateEmailTemplate(status) {
    const template = emailTemplates[status];
    if (template) {
        document.getElementById('modalEmailSubject').value = template.subject;
        document.getElementById('modalEmailBody').value = template.body
            .replace(/{customer_name}/g, currentCustomerName)
            .replace(/{order_number}/g, currentOrderNumber);
    } else {
        document.getElementById('modalEmailSubject').value = '';
        document.getElementById('modalEmailBody').value = '';
    }
}

function closeStatusModal() {
    document.getElementById('statusModal').style.display = 'none';
    currentOrderUuid = null;
}

function submitStatus(sendEmail) {
    const status = document.getElementById('modalStatus').value;
    const subject = document.getElementById('modalEmailSubject').value;
    const body = document.getElementById('modalEmailBody').value;

 fetch(`/admin/orders/${currentOrderUuid}`, {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json'
    },
    body: JSON.stringify({
        _method: 'PUT',
        status: status,
        send_email: sendEmail,
        email_subject: subject,
        email_body: body,
    })
}).then(() => {
    closeStatusModal();
    window.location.reload();
});
}

document.getElementById('statusModal').addEventListener('click', function(e) {
    if (e.target === this) closeStatusModal();
});
</script>