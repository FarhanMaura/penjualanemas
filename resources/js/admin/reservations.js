/**
 * admin/reservations.js — Admin Reservation Management JS
 * Konfirmasi aksi (konfirmasi/tolak reservasi).
 */

document.addEventListener('DOMContentLoaded', () => {
    // Konfirmasi sebelum menolak reservasi
    document.querySelectorAll('[data-confirm-reject]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const name = btn.dataset.customerName ?? 'pelanggan';
            if (confirm(`Tolak reservasi dari "${name}"? Pelanggan akan mendapat notifikasi.`)) {
                btn.closest('form').submit();
            }
        });
    });
});
