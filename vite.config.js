import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // ── CSS ──────────────────────────────────────────────
                'resources/css/app.css',
                'resources/css/admin.css',
                'resources/css/customer.css',
                'resources/css/landing.css',

                // ── JS Global ────────────────────────────────────────
                'resources/js/app.js',

                // ── JS Per Halaman ───────────────────────────────────
                'resources/js/landing.js',
                'resources/js/admin/products.js',
                'resources/js/admin/reservations.js',
                'resources/js/admin/transactions-create.js',
                'resources/js/customer/catalog.js',
                'resources/js/customer/reservations.js',
            ],
            refresh: true,
        }),
    ],
});
