/**
 * reservations.js — Customer Reservation Form Toggles
 */
document.addEventListener('DOMContentLoaded', () => {
    const resType = document.getElementById('reservation_type');
    const productFields = document.getElementById('product_fields');
    const installmentFields = document.getElementById('installment_fields');
    const pawnFields = document.getElementById('pawn_fields');
    const paymentFields = document.getElementById('payment_fields');
    const productId = document.getElementById('product_id');
    const paymentMethod = document.getElementById('payment_method');

    if (!resType) return;

    function toggleFields() {
        const val = resType.value;
        if (val === 'purchase') {
            if (productFields) productFields.style.display = 'block';
            if (productId) productId.setAttribute('required', 'required');
            if (paymentFields) paymentFields.style.display = 'block';
            if (paymentMethod) paymentMethod.setAttribute('required', 'required');
            if (installmentFields) installmentFields.style.display = 'none';
            if (pawnFields) pawnFields.style.display = 'none';
        } else if (val === 'installment') {
            if (productFields) productFields.style.display = 'block';
            if (productId) productId.setAttribute('required', 'required');
            if (paymentFields) paymentFields.style.display = 'block';
            if (paymentMethod) paymentMethod.setAttribute('required', 'required');
            if (installmentFields) installmentFields.style.display = 'block';
            if (pawnFields) pawnFields.style.display = 'none';
        } else if (val === 'pawn') {
            if (productFields) productFields.style.display = 'none';
            if (productId) productId.removeAttribute('required');
            if (paymentFields) paymentFields.style.display = 'none';
            if (paymentMethod) paymentMethod.removeAttribute('required');
            if (installmentFields) installmentFields.style.display = 'none';
            if (pawnFields) pawnFields.style.display = 'block';
        }
    }

    resType.addEventListener('change', toggleFields);
    toggleFields(); // run initially
});
