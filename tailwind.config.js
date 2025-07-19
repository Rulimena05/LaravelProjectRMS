import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    // --- BAGIAN YANG DIUBAH ---
    plugins: [
        forms,
        require('daisyui'), // 1. Tambahkan DaisyUI sebagai plugin
    ],
    
    // 2. Konfigurasi tema DaisyUI (opsional)
    daisyui: {
        themes: ["light", "dark", "cupcake"], // Pilih tema yang Anda suka, "light" akan jadi default
    },
};