/**
 * customer/catalog.js — Customer Product Catalog JS
 * Modal detail produk.
 */

document.addEventListener('DOMContentLoaded', () => {
    // Buka modal detail produk
    document.querySelectorAll('[data-open-product]').forEach(card => {
        card.addEventListener('click', () => {
            const modal = document.getElementById('product-modal');
            if (!modal) return;

            // Isi data modal dari data-* attribute pada kartu
            const fill = (id, val) => {
                const el = document.getElementById(id);
                if (el) el.textContent = val ?? '-';
            };

            fill('modal-product-name',  card.dataset.name);
            fill('modal-product-karat', card.dataset.karat);
            fill('modal-product-berat', card.dataset.berat + ' gram');
            fill('modal-product-harga', card.dataset.harga);
            fill('modal-product-stok',  card.dataset.stok + ' pcs');
            fill('modal-product-desc',  card.dataset.desc);

            const icon = document.getElementById('modal-product-icon');
            if (icon) icon.textContent = card.dataset.icon ?? '💍';

            // Link reservasi
            const reserveBtn = document.getElementById('modal-reserve-btn');
            if (reserveBtn && card.dataset.productId) {
                reserveBtn.href = `/customer/reservations/create?product_id=${card.dataset.productId}`;
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        });
    });

    // Tutup modal
    const closeBtn = document.getElementById('modal-close-btn');
    const modal    = document.getElementById('product-modal');

    const closeModal = () => {
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    };

    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (modal)    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
    });

    // Filter kategori (client-side sederhana)
    document.querySelectorAll('[data-filter-cat]').forEach(btn => {
        btn.addEventListener('click', () => {
            const cat = btn.dataset.filterCat;

            // Update active button style
            document.querySelectorAll('[data-filter-cat]').forEach(b => {
                b.classList.remove('filter-active');
            });
            btn.classList.add('filter-active');

            // Tampilkan/sembunyikan kartu produk
            document.querySelectorAll('[data-open-product]').forEach(card => {
                const cardCat = card.dataset.category ?? '';
                const show    = cat === 'all' || cardCat === cat;
                card.closest('.product-card-wrapper').style.display = show ? '' : 'none';
            });
        });
    });
});
