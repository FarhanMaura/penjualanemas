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

    function toggleFields() {
        const val = resType.value;
        if (val === 'purchase') {
            if (productFields) productFields.style.display = 'block';
            if (productId) productId.setAttribute('required', 'required');
            if (paymentFields) paymentFields.style.display = 'block';
            if (paymentMethod) paymentMethod.setAttribute('required', 'required');
            if (paymentMethodLabel) paymentMethodLabel.textContent = 'Pilih Metode Pembayaran *';
            if (installmentFields) installmentFields.style.display = 'none';
            if (pawnFields) pawnFields.style.display = 'none';
            if (buybackFields) buybackFields.style.display = 'none';
            if (buybackDesc) buybackDesc.removeAttribute('required');
            if (buybackWeight) buybackWeight.removeAttribute('required');
            if (pawnDesc) pawnDesc.removeAttribute('required');
            if (pawnWeight) pawnWeight.removeAttribute('required');
        } else if (val === 'buyback') {
            if (productFields) productFields.style.display = 'none';
            if (productId) productId.removeAttribute('required');
            if (paymentFields) paymentFields.style.display = 'block';
            if (paymentMethod) paymentMethod.setAttribute('required', 'required');
            if (paymentMethodLabel) paymentMethodLabel.textContent = 'Pilih Metode Penerimaan Pembayaran dari Toko *';
            if (installmentFields) installmentFields.style.display = 'none';
            if (pawnFields) pawnFields.style.display = 'none';
            if (buybackFields) buybackFields.style.display = 'block';
            if (buybackDesc) buybackDesc.setAttribute('required', 'required');
            if (buybackWeight) buybackWeight.setAttribute('required', 'required');
            if (pawnDesc) pawnDesc.removeAttribute('required');
            if (pawnWeight) pawnWeight.removeAttribute('required');
        } else if (val === 'installment') {
            if (productFields) productFields.style.display = 'block';
            if (productId) productId.setAttribute('required', 'required');
            if (paymentFields) paymentFields.style.display = 'block';
            if (paymentMethod) paymentMethod.setAttribute('required', 'required');
            if (paymentMethodLabel) paymentMethodLabel.textContent = 'Pilih Metode Pembayaran DP / Angsuran *';
            if (installmentFields) installmentFields.style.display = 'block';
            if (pawnFields) pawnFields.style.display = 'none';
            if (buybackFields) buybackFields.style.display = 'none';
            if (buybackDesc) buybackDesc.removeAttribute('required');
            if (buybackWeight) buybackWeight.removeAttribute('required');
            if (pawnDesc) pawnDesc.removeAttribute('required');
            if (pawnWeight) pawnWeight.removeAttribute('required');
        } else if (val === 'pawn') {
            if (productFields) productFields.style.display = 'none';
            if (productId) productId.removeAttribute('required');
            if (paymentFields) paymentFields.style.display = 'none';
            if (paymentMethod) paymentMethod.removeAttribute('required');
            if (installmentFields) installmentFields.style.display = 'none';
            if (pawnFields) pawnFields.style.display = 'block';
            if (buybackFields) buybackFields.style.display = 'none';
            if (buybackDesc) buybackDesc.removeAttribute('required');
            if (buybackWeight) buybackWeight.removeAttribute('required');
            if (pawnDesc) pawnDesc.setAttribute('required', 'required');
            if (pawnWeight) pawnWeight.setAttribute('required', 'required');
        }
    }

    resType.addEventListener('change', toggleFields);
    toggleFields(); // run initially
});

