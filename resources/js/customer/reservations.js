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

    if (!resType) return;

    function setSectionState(sectionEl, isVisible, isRequired = false) {
        if (!sectionEl) return;
        sectionEl.style.display = isVisible ? 'block' : 'none';
        const inputs = sectionEl.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.disabled = !isVisible;
            if (isRequired && isVisible) {
                // If required is set on input specifically
            } else if (!isVisible) {
                input.removeAttribute('required');
            }
        });
    }

    function toggleFields() {
        const val = resType.value;
        if (val === 'purchase') {
            setSectionState(productFields, true);
            setSectionState(paymentFields, true);
            setSectionState(installmentFields, false);
            setSectionState(pawnFields, false);
            setSectionState(buybackFields, false);

            if (productId) productId.setAttribute('required', 'required');
            if (paymentMethod) paymentMethod.setAttribute('required', 'required');
            if (paymentMethodLabel) paymentMethodLabel.textContent = 'Pilih Metode Pembayaran *';
        } else if (val === 'buyback') {
            setSectionState(productFields, false);
            setSectionState(installmentFields, false);
            setSectionState(pawnFields, false);
            setSectionState(buybackFields, true);
            setSectionState(paymentFields, true);

            if (buybackDesc) buybackDesc.setAttribute('required', 'required');
            if (buybackWeight) buybackWeight.setAttribute('required', 'required');
            if (paymentMethod) paymentMethod.setAttribute('required', 'required');
            if (paymentMethodLabel) paymentMethodLabel.textContent = 'Pilih Metode Penerimaan Pembayaran dari Toko *';
        } else if (val === 'installment') {
            setSectionState(productFields, true);
            setSectionState(installmentFields, true);
            setSectionState(paymentFields, true);
            setSectionState(pawnFields, false);
            setSectionState(buybackFields, false);

            if (productId) productId.setAttribute('required', 'required');
            if (paymentMethod) paymentMethod.setAttribute('required', 'required');
            if (paymentMethodLabel) paymentMethodLabel.textContent = 'Pilih Metode Pembayaran DP / Angsuran *';
        } else if (val === 'pawn') {
            setSectionState(productFields, false);
            setSectionState(installmentFields, false);
            setSectionState(paymentFields, false);
            setSectionState(buybackFields, false);
            setSectionState(pawnFields, true);

            if (pawnDesc) pawnDesc.setAttribute('required', 'required');
            if (pawnWeight) pawnWeight.setAttribute('required', 'required');
        }
    }

    resType.addEventListener('change', toggleFields);
    toggleFields(); // run initially
});

