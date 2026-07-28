/**
 * landing.js — Public Landing Page Logic
 * Manages the product modal pop-ups.
 */

function openProductModal(productId, e) {
    if (e && e.target && e.target.closest('a, button, input, select')) return;
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

/**
 * Filter product grid by category or basic status.
 */
function filterCategory(categorySlug, btnEl) {
    const buttons = document.querySelectorAll('.category-filter-btn');
    buttons.forEach(b => {
        b.classList.remove('bg-amber-500', 'text-gray-950', 'font-bold', 'shadow-lg');
        b.classList.add('glass', 'text-gray-300', 'hover:bg-white/10');
    });

    if (btnEl) {
        btnEl.classList.remove('glass', 'text-gray-300', 'hover:bg-white/10');
        btnEl.classList.add('bg-amber-500', 'text-gray-950', 'font-bold', 'shadow-lg');
    }

    const cards = document.querySelectorAll('#product-grid > [data-product-id]');
    let visibleCount = 0;

    cards.forEach(card => {
        const catSlug = card.dataset.categorySlug;

        let show = false;
        if (categorySlug === 'all' || !categorySlug) {
            show = true;
        } else {
            show = (catSlug === categorySlug);
        }

        if (show) {
            card.classList.remove('hidden');
            visibleCount++;
        } else {
            card.classList.add('hidden');
        }
    });

    const emptyState = document.getElementById('product-empty-state');
    if (emptyState) {
        if (visibleCount === 0) {
            emptyState.classList.remove('hidden');
        } else {
            emptyState.classList.add('hidden');
        }
    }
}

// Auto filter to all on DOM ready if product grid exists
document.addEventListener('DOMContentLoaded', () => {
    const defaultBtn = document.querySelector('.category-filter-btn[data-default="true"]');
    if (defaultBtn) {
        filterCategory('all', defaultBtn);
    }
});

// Expose functions to window scope for onclick handlers
window.openProductModal = openProductModal;
window.closeModal = closeModal;
window.filterCategory = filterCategory;
