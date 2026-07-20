/**
 * transactions-create.js — Admin Transaction Form Dynamic Elements
 */
document.addEventListener('DOMContentLoaded', () => {
    const PRODUCTS = window.PRODUCTS_DATA || [];
    const productMap = {};
    PRODUCTS.forEach(p => productMap[p.id] = p);

    let itemIdx = 1;

    function getSelectHtml(idx) {
        let opts = `<option value="">-- Pilih Produk --</option>`;
        PRODUCTS.forEach(p => {
            opts += `<option value="${p.id}" data-price="${p.base_price}">${p.name} — Stok: ${p.stock}</option>`;
        });
        return `<select name="items[${idx}][product_id]" class="input-field item-product" required>${opts}</select>`;
    }

    const itemsContainer = document.getElementById('items-container');
    const addItemBtn = document.getElementById('add-item-btn');

    if (addItemBtn && itemsContainer) {
        addItemBtn.addEventListener('click', () => {
            const row = document.createElement('div');
            row.className = 'item-row grid grid-cols-12 gap-3 items-end p-3 rounded-xl';
            row.style.cssText = 'background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06);';
            row.innerHTML = `
                <div class="col-span-5">
                    <label class="input-label">Produk *</label>
                    ${getSelectHtml(itemIdx)}
                </div>
                <div class="col-span-2">
                    <label class="input-label">Qty</label>
                    <input type="number" name="items[${itemIdx}][quantity]" value="1" class="input-field item-qty" min="1" required>
                </div>
                <div class="col-span-4">
                    <label class="input-label">Harga Satuan (Rp)</label>
                    <input type="number" name="items[${itemIdx}][unit_price]" value="0" class="input-field item-price" min="0" placeholder="0" required>
                </div>
                <div class="col-span-1 flex items-end pb-0.5">
                    <button type="button" class="btn-danger w-full remove-item-btn">✕</button>
                </div>`;
            itemsContainer.appendChild(row);
            
            // Set change listener on newly added select element
            const selectEl = row.querySelector('.item-product');
            selectEl.addEventListener('change', () => fillPrice(selectEl));

            itemIdx++;
            updateRemoveButtons();
            recalculate();
        });

        itemsContainer.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-item-btn')) {
                e.target.closest('.item-row').remove();
                updateRemoveButtons();
                recalculate();
            }
        });
    }

    // Set change listener on initial row product select
    const initialSelect = document.querySelector('.item-product');
    if (initialSelect) {
        initialSelect.addEventListener('change', () => fillPrice(initialSelect));
    }

    function fillPrice(selectEl) {
        const productId = selectEl.value;
        const row = selectEl.closest('.item-row');
        if (!row) return;
        const priceInput = row.querySelector('.item-price');
        if (priceInput) {
            if (productId && productMap[productId]) {
                priceInput.value = productMap[productId].base_price;
            } else {
                priceInput.value = 0;
            }
        }
        recalculate();
    }

    function updateRemoveButtons() {
        const rows = document.querySelectorAll('.item-row');
        rows.forEach(row => {
            const btn = row.querySelector('.remove-item-btn');
            if (btn) btn.style.display = rows.length > 1 ? '' : 'none';
        });
    }

    const typeSelect = document.getElementById('transaction_type');
    const installmentSection = document.getElementById('installment-extra-fields');
    const pawnSection = document.getElementById('pawn-extra-fields');
    const itemsSection = document.getElementById('items-section');

    if (typeSelect) {
        typeSelect.addEventListener('change', toggleFields);
        toggleFields(); // Run initially
    }

    function toggleFields() {
        const type = typeSelect.value;
        if (type === 'installment') {
            if (installmentSection) installmentSection.style.display = 'grid';
            if (pawnSection) pawnSection.style.display = 'none';
            if (itemsSection) itemsSection.style.display = 'block';

            const instTenure = document.getElementById('installment_tenure');
            const instDp = document.getElementById('installment_down_payment');
            if (instTenure) instTenure.setAttribute('required', 'required');
            if (instDp) instDp.setAttribute('required', 'required');
            setPawnRequired(false);
        } else if (type === 'pawn') {
            if (installmentSection) installmentSection.style.display = 'none';
            if (pawnSection) pawnSection.style.display = 'grid';
            if (itemsSection) itemsSection.style.display = 'none';

            setPawnRequired(true);
            const instTenure = document.getElementById('installment_tenure');
            const instDp = document.getElementById('installment_down_payment');
            if (instTenure) instTenure.removeAttribute('required');
            if (instDp) instDp.removeAttribute('required');
        } else {
            if (installmentSection) installmentSection.style.display = 'none';
            if (pawnSection) pawnSection.style.display = 'none';
            if (itemsSection) itemsSection.style.display = 'block';

            const instTenure = document.getElementById('installment_tenure');
            const instDp = document.getElementById('installment_down_payment');
            if (instTenure) instTenure.removeAttribute('required');
            if (instDp) instDp.removeAttribute('required');
            setPawnRequired(false);
        }
        recalculate();
    }

    function setPawnRequired(val) {
        const fields = [
            'pawn_gold_description', 'pawn_gold_purity', 'pawn_weight_gram',
            'pawn_appraised_value', 'pawn_loan_amount', 'pawn_interest_rate', 'pawn_due_date'
        ];
        fields.forEach(f => {
            const el = document.getElementById(f) || document.getElementsByName(f)[0];
            if (el) {
                if (val) el.setAttribute('required', 'required');
                else el.removeAttribute('required');
            }
        });
    }

    // Auto-calculate pawn due date (+4 months)
    const paymentDateEl = document.getElementsByName('payment_date')[0];
    if (paymentDateEl) {
        paymentDateEl.addEventListener('change', (e) => {
            const dateVal = e.target.value;
            if (dateVal) {
                const dt = new Date(dateVal);
                dt.setMonth(dt.getMonth() + 4);
                const yyyy = dt.getFullYear();
                let mm = dt.getMonth() + 1;
                if (mm < 10) mm = '0' + mm;
                let dd = dt.getDate();
                if (dd < 10) dd = '0' + dd;
                const pawnDueDateEl = document.getElementsByName('pawn_due_date')[0];
                if (pawnDueDateEl) {
                    pawnDueDateEl.value = `${yyyy}-${mm}-${dd}`;
                }
            }
        });
    }

    function recalculate() {
        if (!typeSelect) return;
        const type = typeSelect.value;
        if (type === 'pawn') {
            const loanVal = document.getElementById('pawn_loan_amount')?.value;
            const loan = parseFloat(loanVal || 0);
            const totalDisplay = document.getElementById('total-display');
            if (totalDisplay) {
                totalDisplay.textContent = 'Rp ' + loan.toLocaleString('id-ID');
            }
            return;
        }

        let subtotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const qtyVal = row.querySelector('.item-qty')?.value;
            const priceVal = row.querySelector('.item-price')?.value;
            const qty = parseFloat(qtyVal || 0);
            const price = parseFloat(priceVal || 0);
            subtotal += qty * price;
        });
        const feeVal = document.querySelector('[name=admin_fee]')?.value;
        const discVal = document.querySelector('[name=discount]')?.value;
        const fee = parseFloat(feeVal || 0);
        const discount = parseFloat(discVal || 0);
        const total = subtotal + fee - discount;
        const totalDisplay = document.getElementById('total-display');
        if (totalDisplay) {
            totalDisplay.textContent = 'Rp ' + Math.max(0, total).toLocaleString('id-ID');
        }
    }

    const trxForm = document.getElementById('trx-form');
    if (trxForm) {
        trxForm.addEventListener('input', recalculate);
    }
});
