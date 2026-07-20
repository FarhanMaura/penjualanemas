/**
 * app.js — Global JS Entry Point
 * Hanya inisialisasi Alpine.js dan global utilities.
 * Logic per-halaman ada di file JS masing-masing.
 */

import Alpine from 'alpinejs';
import focus  from '@alpinejs/focus';

window.Alpine = Alpine;
Alpine.plugin(focus);
Alpine.start();

/**
 * Flash message: auto-hide setelah 5 detik
 */
document.addEventListener('DOMContentLoaded', () => {
    const flashes = document.querySelectorAll('[data-flash]');
    flashes.forEach(el => {
        setTimeout(() => {
            el.style.transition = 'opacity 0.4s';
            el.style.opacity    = '0';
            setTimeout(() => el.remove(), 400);
        }, 5000);
    });
});
