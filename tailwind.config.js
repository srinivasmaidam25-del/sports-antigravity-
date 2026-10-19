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
                sans: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
                display: ['Outfit', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#f0fdf4',
                    100: '#dcfce7',
                    200: '#bbf7d0',
                    300: '#86efac',
                    400: '#4ade80',
                    500: '#22c55e', // Pitch Green
                    600: '#16a34a',
                    700: '#15803d',
                    800: '#166534',
                    900: '#14532d',
                    950: '#052e16',
                },
                surface: {
                    50: '#f8fafc',
                    100: '#f1f5f9',
                    200: '#e2e8f0',
                    300: '#cbd5e1',
                    400: '#94a3b8',
                    500: '#64748b',
                    600: '#475569',
                    700: '#334155',
                    800: '#1e293b',
                    900: '#0f172a', // Midnight Blue
                    950: '#020617', // Deep Dark
                },
                accent: {
                    gold: '#fbbf24',
                    orange: '#f97316',
                    blue: '#3b82f6',
                }
            },
            boxShadow: {
                'glow': '0 0 20px rgba(34, 197, 94, 0.15)',
                'glow-lg': '0 0 30px rgba(34, 197, 94, 0.25)',
                'glass': 'inset 0 1px 0 0 rgba(255, 255, 255, 0.05)',
            },
            backgroundImage: {
                'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
                'pitch-pattern': "url('data:image/svg+xml,%3Csvg width=\"20\" height=\"20\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cpath d=\"M0 0h20v20H0z\" fill=\"%23000\" fill-opacity=\"0.02\"/%3E%3Cpath d=\"M0 0h1v20H0zM0 0h20v1H0z\" fill=\"%23fff\" fill-opacity=\"0.03\"/%3E%3C/svg%3E')",
            }
        },
    },

    plugins: [forms],
};
