/**
 * transactions-create.js — Admin Transaction Form Dynamic Elements
 */
document.addEventListener('DOMContentLoaded', () => {
    const PRODUCTS = window.PRODUCTS_DATA || [];
    const productMap = {};
    PRODUCTS.forEach(p => productMap[p.id] = p);

    const RESERVATIONS = window.RESERVATIONS_DATA || [];
    const reservationMap = {};
    RESERVATIONS.forEach(r => reservationMap[r.id] = r);

    const reservationSelect = document.getElementById('reservation_select');
    if (reservationSelect) {
        reservationSelect.addEventListener('change', () => {
            const resId = reservationSelect.value;
            if (!resId || !reservationMap[resId]) return;
            const res = reservationMap[resId];

            // Auto-fill customer
            const userSelect = document.querySelector('[name=user_id]');
            if (userSelect && res.user_id) userSelect.value = res.user_id;

            // Auto-fill type
            const typeSel = document.getElementById('transaction_type');
            if (typeSel && res.type) {
                typeSel.value = res.type;
                toggleFields();
            }

            // Auto-fill payment method
            const paySelect = document.getElementById('transaction_payment_method');
            if (paySelect && res.payment_method) paySelect.value = res.payment_method;

            // Auto-fill notes
            const notesEl = document.querySelector('[name=notes]');
            if (notesEl && res.notes) notesEl.value = res.notes;

            // Auto-fill item product, qty & unit_price
            if (res.product_id) {
                const prodSelect = document.querySelector('.item-product');
                if (prodSelect) prodSelect.value = res.product_id;

                const qtyInput = document.querySelector('.item-qty');
                if (qtyInput) qtyInput.value = res.quantity || 1;

                const priceInput = document.querySelector('.item-price');
                if (priceInput && res.unit_price) priceInput.value = res.unit_price;
            }


            // Auto-fill installment
            if (res.type === 'installment') {
                const tenureEl = document.getElementById('installment_tenure');
                if (tenureEl && res.installment_tenure) tenureEl.value = res.installment_tenure;

                const dpEl = document.getElementById('installment_down_payment');
                if (dpEl && res.installment_down_payment !== null) dpEl.value = res.installment_down_payment;
            }

            // Auto-fill pawn
            if (res.type === 'pawn') {
                const descEl = document.getElementById('pawn_gold_description');
                if (descEl && res.pawn_gold_description) descEl.value = res.pawn_gold_description;

                const purityEl = document.getElementById('pawn_gold_purity');
                if (purityEl && res.pawn_gold_purity) purityEl.value = res.pawn_gold_purity;

                const weightEl = document.getElementById('pawn_weight_gram');
                if (weightEl && res.pawn_weight_gram) weightEl.value = res.pawn_weight_gram;

                const loanEl = document.getElementById('pawn_loan_amount');
                if (loanEl && res.pawn_amount_requested) loanEl.value = res.pawn_amount_requested;

                const appraisedEl = document.getElementById('pawn_appraised_value');
                if (appraisedEl && res.pawn_amount_requested) appraisedEl.value = res.pawn_amount_requested;
            }

            recalculate();
        });
    }

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
                    <label class="input-label">Produk <span class="text-red-600">*</span></label>
                    ${getSelectHtml(itemIdx)}
                </div>
                <div class="col-span-2">
                    <label class="input-label">Qty <span class="text-red-600">*</span></label>
                    <input type="number" name="items[${itemIdx}][quantity]" value="1" class="input-field item-qty" min="1" required>
                </div>
                <div class="col-span-4">
                    <label class="input-label">Harga Satuan (Rp) <span class="text-xs text-slate-500 font-normal">(Otomatis Produk)</span> <span class="text-red-600">*</span></label>
                    <input type="number" name="items[${itemIdx}][unit_price]" class="input-field item-price font-extrabold text-slate-800 bg-slate-100 cursor-not-allowed border-slate-300" min="0" placeholder="0" readonly required tabindex="-1">
                </div>
                <div class="col-span-1 flex items-end pb-0.5">
                    <button type="button" class="btn-danger w-full remove-item-btn">✕</button>
                </div>
            `;
            itemsContainer.appendChild(row);
            
            const priceEl = row.querySelector('.item-price');
            if (priceEl && window.attachRupiahFormat) window.attachRupiahFormat(priceEl);

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
            const rawPrice = (productId && productMap[productId]) ? productMap[productId].base_price : 0;
            priceInput.value = window.formatRupiah ? window.formatRupiah(rawPrice) : rawPrice;
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
    const kalkulasiSection = document.getElementById('kalkulasi-section');

    if (typeSelect) {
        typeSelect.addEventListener('change', toggleFields);
        toggleFields(); // Run initially
    }

    function toggleFields() {
        const type = typeSelect.value;
        if (type === 'buyback') {
            if (installmentSection) installmentSection.style.display = 'none';
            if (pawnSection) pawnSection.style.display = 'none';
            if (itemsSection) itemsSection.style.display = 'none';
            if (kalkulasiSection) kalkulasiSection.style.display = 'none';

            setPawnRequired(false);
            setItemRequired(false);
            const instTenure = document.getElementById('installment_tenure');
            if (instTenure) instTenure.removeAttribute('required');
        } else if (type === 'installment') {
            if (installmentSection) installmentSection.style.display = 'grid';
            if (pawnSection) pawnSection.style.display = 'none';
            if (itemsSection) itemsSection.style.display = 'block';
            if (kalkulasiSection) kalkulasiSection.style.display = 'block';

            setPawnRequired(false);
            setItemRequired(true);
            const instTenure = document.getElementById('installment_tenure');
            if (instTenure) instTenure.setAttribute('required', 'required');
        } else if (type === 'pawn') {
            if (installmentSection) installmentSection.style.display = 'none';
            if (pawnSection) pawnSection.style.display = 'grid';
            if (itemsSection) itemsSection.style.display = 'none';
            if (kalkulasiSection) kalkulasiSection.style.display = 'block';

            setPawnRequired(true);
            setItemRequired(false);
            const instTenure = document.getElementById('installment_tenure');
            if (instTenure) instTenure.removeAttribute('required');
        } else {
            if (installmentSection) installmentSection.style.display = 'none';
            if (pawnSection) pawnSection.style.display = 'none';
            if (itemsSection) itemsSection.style.display = 'block';
            if (kalkulasiSection) kalkulasiSection.style.display = 'block';

            setPawnRequired(false);
            setItemRequired(true);
            const instTenure = document.getElementById('installment_tenure');
            if (instTenure) instTenure.removeAttribute('required');
        }
        recalculate();
    }

    function setItemRequired(val) {
        document.querySelectorAll('#items-section select, #items-section input, #items-section button').forEach(el => {
            if (val) {
                el.removeAttribute('disabled');
                if (el.classList.contains('item-product') || el.classList.contains('item-qty') || el.classList.contains('item-price')) {
                    el.setAttribute('required', 'required');
                }
            } else {
                el.removeAttribute('required');
                el.setAttribute('disabled', 'disabled');
            }
        });
    }

    function setPawnRequired(val) {
        const fields = [
            'pawn_gold_description', 'pawn_gold_purity', 'pawn_weight_gram',
            'pawn_appraised_value', 'pawn_loan_amount', 'pawn_tenure', 'pawn_due_date'
        ];
        fields.forEach(f => {
            const el = document.getElementById(f) || document.getElementsByName(f)[0];
            if (el) {
                if (val) el.setAttribute('required', 'required');
                else el.removeAttribute('required');
            }
        });
    }

    // Auto-calculate pawn due date (+N months) and monthly installment
    function updatePawnDueDateAndInstallment() {
        const paymentDateEl = document.getElementsByName('payment_date')[0];
        const tenureEl = document.getElementById('pawn_tenure');
        const loanEl = document.getElementById('pawn_loan_amount');
        const dueEl = document.getElementById('pawn_due_date');
        const monthlyDisp = document.getElementById('pawn_monthly_display');

        const tenure = parseInt(tenureEl?.value || 4, 10);
        const dateVal = paymentDateEl?.value;
        if (dateVal && dueEl) {
            const dt = new Date(dateVal);
            dt.setMonth(dt.getMonth() + tenure);
            const yyyy = dt.getFullYear();
            let mm = dt.getMonth() + 1;
            if (mm < 10) mm = '0' + mm;
            let dd = dt.getDate();
            if (dd < 10) dd = '0' + dd;
            dueEl.value = `${yyyy}-${mm}-${dd}`;
        }

        if (monthlyDisp) {
            const parseR = window.parseRupiah || function(v) { return parseFloat(v) || 0; };
            const loan = parseR(loanEl?.value);
            const monthly = tenure > 0 ? Math.round(loan / tenure) : loan;
            monthlyDisp.textContent = 'Rp ' + monthly.toLocaleString('id-ID') + ` / bulan (${tenure}x Angsuran)`;
        }
    }

    const paymentDateEl = document.getElementsByName('payment_date')[0];
    if (paymentDateEl) {
        paymentDateEl.addEventListener('change', updatePawnDueDateAndInstallment);
    }
    const pawnTenureEl = document.getElementById('pawn_tenure');
    if (pawnTenureEl) {
        pawnTenureEl.addEventListener('change', updatePawnDueDateAndInstallment);
    }
    const pawnLoanEl = document.getElementById('pawn_loan_amount');
    if (pawnLoanEl) {
        pawnLoanEl.addEventListener('input', updatePawnDueDateAndInstallment);
    }

    function recalculate() {
        if (!typeSelect) return;
        const parseR = window.parseRupiah || function(v) { return parseFloat(v) || 0; };
        const type = typeSelect.value;
        if (type === 'buyback') {
            return;
        }
        if (type === 'pawn') {
            updatePawnDueDateAndInstallment();
            const loanVal = document.getElementById('pawn_loan_amount')?.value;
            const loan = parseR(loanVal);
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
            const price = parseR(priceVal);
            subtotal += qty * price;
        });
        const feeVal = document.querySelector('[name=admin_fee]')?.value;
        const fee = parseR(feeVal);

        // Diskon Otomatis berbasis Persentase (Poin 3)
        const discPercentEl = document.getElementById('discount_percentage');
        const discAmountEl = document.getElementById('discount_amount');
        const discDisplayEl = document.getElementById('discount-display');

        let discount = 0;
        if (discPercentEl) {
            const discountPercent = parseFloat(discPercentEl?.value || 0);
            discount = Math.round(subtotal * (discountPercent / 100));

            if (discAmountEl) {
                discAmountEl.value = discount;
            }
            if (discDisplayEl) {
                discDisplayEl.textContent = 'Rp ' + discount.toLocaleString('id-ID') + ' (' + discountPercent + '%)';
            }
        } else {
            const discVal = document.querySelector('[name=discount]')?.value;
            discount = parseR(discVal);
        }
        const total = subtotal + fee - discount;
        const totalDisplay = document.getElementById('total-display');
        if (totalDisplay) {
            totalDisplay.textContent = 'Rp ' + Math.max(0, total).toLocaleString('id-ID');
        }
    }

    const discPercentEl = document.getElementById('discount_percentage');
    if (discPercentEl) {
        discPercentEl.addEventListener('change', recalculate);
    }

    const trxForm = document.getElementById('trx-form');
    if (trxForm) {
        trxForm.addEventListener('input', recalculate);
        trxForm.addEventListener('change', recalculate);
    }
});
