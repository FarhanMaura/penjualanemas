/**
 * landing.js — Public Landing Page Logic
 * Manages the product modal pop-ups.
 */

function openProductModal(productId) {
    const card = document.querySelector(`[data-product-id="${productId}"]`);
    if (!card) return;

    const icons = ['🪙','🥇','⭐','✨','💫'];
    const cards = document.querySelectorAll('[data-product-id]');
    let idx = 0;
    cards.forEach((c, i) => { if (c.dataset.productId == productId) idx = i; });

    const modalIcon = document.getElementById('modal-icon');
    const modalImg = document.getElementById('modal-img');

    // Handle thumbnail display if present
    if (card.dataset.image && card.dataset.image !== '') {
        if (modalImg) {
            modalImg.src = card.dataset.image;
            modalImg.classList.remove('hidden');
        }
        if (modalIcon) {
            modalIcon.classList.add('hidden');
        }
    } else {
        if (modalImg) {
            modalImg.classList.add('hidden');
        }
        if (modalIcon) {
            modalIcon.textContent = icons[idx % icons.length];
            modalIcon.classList.remove('hidden');
        }
    }

    document.getElementById('modal-badge').textContent = card.dataset.purity + ' • Kadar Emas';
    document.getElementById('modal-name').textContent  = card.dataset.name;
    document.getElementById('modal-berat').textContent = card.dataset.weight;
    document.getElementById('modal-kadar').textContent = card.dataset.purity;
    document.getElementById('modal-harga').textContent = card.dataset.price;
    document.getElementById('modal-stok').textContent  = card.dataset.stock;
    document.getElementById('modal-desc').textContent  = card.dataset.desc;

    // Update reservation button link with product_id parameter
    const reserveBtn = document.getElementById('modal-reserve-btn');
    if (reserveBtn) {
        try {
            const url = new URL(reserveBtn.href);
            url.searchParams.set('product_id', productId);
            reserveBtn.href = url.toString();
        } catch (e) {
            // Fallback if URL is relative or not valid
            reserveBtn.href = "/customer/reservations/create?product_id=" + productId;
        }
    }

    const modal = document.getElementById('modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeModal(e) {
    if (e && e.currentTarget !== e.target && e.target.id !== 'modal' && e.target.textContent !== '×') return;
    const modal = document.getElementById('modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Close modal when pressing Escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeModal();
});

// Expose functions to window scope for onclick handlers
window.openProductModal = openProductModal;
window.closeModal = closeModal;
