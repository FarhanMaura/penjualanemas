/**
 * reservations.js — Customer Reservation Form Toggles
 */
document.addEventListener('DOMContentLoaded', () => {
    const resType = document.getElementById('reservation_type');
    const productFields = document.getElementById('product_fields');
    const installmentFields = document.getElementById('installment_fields');
    const pawnFields = document.getElementById('pawn_fields');
    const buybackFields = document.getElementById('buyback_fields');
    const paymentFields = document.getElementById('payment_fields');
    const paymentMethodLabel = document.getElementById('payment_method_label');
    const productId = document.getElementById('product_id');
    const paymentMethod = document.getElementById('payment_method');
    const buybackDesc = document.getElementById('buyback_gold_description');
    const buybackWeight = document.getElementById('buyback_weight_gram');
    const pawnDesc = document.getElementById('pawn_gold_description');
    const pawnWeight = document.getElementById('pawn_weight_gram');

    const scheduleFields = document.getElementById('schedule_fields');
    const preferredDate = document.getElementById('preferred_date');
    const preferredTime = document.getElementById('preferred_time');

    if (!resType) return;

    function setSectionState(sectionEl, isVisible, isRequired = false) {
        if (!sectionEl) return;
        sectionEl.style.display = isVisible ? '' : 'none';
        const inputs = sectionEl.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.disabled = !isVisible;
            if (isRequired && isVisible) {
                input.setAttribute('required', 'required');
            } else if (!isVisible) {
                input.removeAttribute('required');
            }
        });
    }

    function filterPaymentMethods(isBuyback) {
        if (!paymentMethod) return;
        const options = paymentMethod.querySelectorAll('option');
        const optgroups = paymentMethod.querySelectorAll('optgroup');

        optgroups.forEach(og => {
            const label = (og.label || '').toLowerCase();
            if (isBuyback) {
                if (label.includes('tunai') || label.includes('cash')) {
                    og.hidden = false;
                    og.style.display = '';
                } else {
                    og.hidden = true;
                    og.style.display = 'none';
                }
            } else {
                og.hidden = false;
                og.style.display = '';
            }
        });

        options.forEach(opt => {
            const isCash = opt.value === 'cash' || opt.value === 'tunai';
            if (isBuyback) {
                if (isCash) {
                    opt.hidden = false;
                    opt.disabled = false;
                    opt.style.display = '';
                } else {
                    opt.hidden = true;
                    opt.disabled = true;
                    opt.style.display = 'none';
                }
            } else {
                opt.hidden = false;
                opt.disabled = false;
                opt.style.display = '';
            }
        });

        if (isBuyback) {
            paymentMethod.value = 'cash';
        }
    }

    const paymentWrapper = document.getElementById('payment_method_select_wrapper');
    const paymentMethodInfo = document.getElementById('payment_method_info');

    function toggleFields() {
        const val = resType.value;
        const isBuyback = val === 'buyback';

        // Untuk buyback: sembunyikan seluruh payment_fields (tidak ada metode pembayaran)
        if (isBuyback) {
            if (paymentFields) {
                paymentFields.style.display = 'none';
                // Disable semua input dalam payment_fields agar tidak ter-submit
                paymentFields.querySelectorAll('input, select, textarea').forEach(el => {
                    el.disabled = true;
                    el.removeAttribute('required');
                });
            }
        } else {
            // Non-buyback: tampilkan payment_fields, restore select
            if (paymentMethod) {
                paymentMethod.disabled = false;
                // Restore ke bca jika sebelumnya dari mode buyback
                if (!paymentMethod.value || paymentMethod.value === 'cash') {
                    paymentMethod.value = 'bca';
                }
            }
        }

        if (val === 'purchase') {
            setSectionState(productFields, true);
            setSectionState(paymentFields, true);
            setSectionState(installmentFields, false);
            setSectionState(pawnFields, false);
            setSectionState(buybackFields, false);
            setSectionState(scheduleFields, true, true);

            if (productId) productId.setAttribute('required', 'required');
            if (paymentMethod) paymentMethod.setAttribute('required', 'required');
            if (paymentMethodLabel) paymentMethodLabel.textContent = 'Pilih Metode Pembayaran *';

            if (window.onPaymentMethodChange && paymentMethod) {
                window.onPaymentMethodChange(paymentMethod.value);
            }
        } else if (val === 'buyback') {
            setSectionState(productFields, false);
            setSectionState(installmentFields, false);
            setSectionState(pawnFields, false);
            setSectionState(buybackFields, true);
            // payment_fields sudah disembunyikan di atas — tidak perlu setSectionState
            setSectionState(scheduleFields, true, true);

            if (buybackDesc) buybackDesc.setAttribute('required', 'required');
            if (paymentMethod) {
                paymentMethod.disabled = true;
                paymentMethod.removeAttribute('required');
            }
        } else if (val === 'installment') {
            setSectionState(productFields, true);
            setSectionState(installmentFields, true);
            setSectionState(paymentFields, true);
            setSectionState(pawnFields, false);
            setSectionState(buybackFields, false);
            setSectionState(scheduleFields, false, false);

            if (productId) productId.setAttribute('required', 'required');
            if (paymentMethod) paymentMethod.setAttribute('required', 'required');
            if (paymentMethodLabel) paymentMethodLabel.textContent = 'Pilih Metode Pembayaran Angsuran *';

            if (window.onPaymentMethodChange && paymentMethod) {
                window.onPaymentMethodChange(paymentMethod.value);
            }
        } else if (val === 'pawn') {
            setSectionState(productFields, false);
            setSectionState(installmentFields, false);
            setSectionState(paymentFields, false);
            setSectionState(buybackFields, false);
            setSectionState(pawnFields, true);
            setSectionState(scheduleFields, true, true);

            if (pawnDesc) pawnDesc.setAttribute('required', 'required');
            if (pawnWeight) pawnWeight.setAttribute('required', 'required');
        }
    }

    resType.addEventListener('change', toggleFields);
    toggleFields(); // run initially

    if (productId) {
        productId.addEventListener('change', () => {
            if (window.updateSelectedProductCard) {
                window.updateSelectedProductCard(productId.value);
            }
        });
    }
});

