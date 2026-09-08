/**
 * app.js — Global JS Entry Point
 * Inisialisasi Alpine.js & global Rupiah auto-formatter (otomatis nitik ribuan).
 */

import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

/**
 * Utility: Format angka ke format Rupiah dengan titik ribuan (misal: 500000 -> 500.000)
 */
window.formatRupiah = function(value) {
    if (value === null || value === undefined || value === '') return '';
    const num = window.parseRupiah(value);
    if (!num && num !== 0) return '';
    return num.toLocaleString('id-ID');
};

/**
 * Utility: Parse string ber-titik / decimal kembali ke angka murni (misal: 500.000 -> 500000, "810000.00" -> 810000)
 */
window.parseRupiah = function(value) {
    if (value === null || value === undefined || value === '') return 0;
    if (typeof value === 'number') return Math.round(value);

    let str = String(value).trim();
    if (!str) return 0;

    // Hapus awalan "Rp" dan spasi jika ada
    str = str.replace(/Rp\s?/gi, '').trim();

    // Jika berupa angka murni tanpa titik (misal: "2600000")
    if (/^\d+$/.test(str)) {
        return parseInt(str, 10);
    }

    // Jika format float SQL murni dengan .00 di akhir tanpa titik ribuan (misal: "2600000.00")
    if (/^\d+\.00$/.test(str)) {
        return parseInt(str.split('.')[0], 10);
    }

    // Format Indonesia (misal: "2.600.000,00" atau "2.600,00"): buang desimal koma jika ada
    if (str.includes(',')) {
        str = str.split(',')[0];
    }

    // Buang semua karakter selain angka (menghapus semua titik ribuan & spasi)
    str = str.replace(/\D/g, '');

    return parseInt(str, 10) || 0;
};

/**
 * Pasang listener auto-format ribuan pada elemen input
 */
window.attachRupiahFormat = function(input) {
    if (!input || input.dataset.rupiahBound) return;
    input.dataset.rupiahBound = 'true';

    // Ubah type="number" menjadi type="text" & inputmode="numeric" agar titik bisa ditampilkan
    if (input.type === 'number') {
        input.type = 'text';
        input.inputMode = 'numeric';
    }

    // Format nilai awal jika sudah ada
    if (input.value) {
        input.value = window.formatRupiah(input.value);
    }

    input.addEventListener('input', (e) => {
        const rawVal = e.target.value;
        const cursorPos = e.target.selectionStart;
        const oldLen = rawVal.length;

        const formatted = window.formatRupiah(rawVal);
        e.target.value = formatted;

        const newLen = formatted.length;
        let newCursorPos = cursorPos + (newLen - oldLen);
        if (newCursorPos < 0) newCursorPos = 0;
        try {
            e.target.setSelectionRange(newCursorPos, newCursorPos);
        } catch(err) {}
    });
};

document.addEventListener('DOMContentLoaded', () => {
    // Auto-hide flash message setelah 5 detik
    const flashes = document.querySelectorAll('[data-flash]');
    flashes.forEach(el => {
        setTimeout(() => {
            el.style.transition = 'opacity 0.4s';
            el.style.opacity    = '0';
            setTimeout(() => el.remove(), 400);
        }, 5000);
    });

    // Inisialisasi semua input ber-class .format-rupiah atau data-rupiah
    document.querySelectorAll('.format-rupiah, [data-rupiah]').forEach(window.attachRupiahFormat);

    // Auto-clean titik saat form disubmit agar backend Laravel menerima integer murni (misal 500000)
    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (form && form.tagName === 'FORM') {
            form.querySelectorAll('.format-rupiah, [data-rupiah]').forEach(input => {
                input.value = window.parseRupiah(input.value);
            });
        }
    }, true);
});
