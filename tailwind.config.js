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
                serif: ['Playfair Display', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                primary: {
                    DEFAULT: '#F4D7D4',
                    dark: '#B76E79',
                    hover: '#E5C4C1',
                },
                surface: '#FBF9F8',
                container: '#F6F3F2',
                onsurface: '#1C1B1B',
                onvariant: '#49454F',
                outline: '#DCD9D9',
                brand: {
                    50:  '#fdf2f2',
                    100: '#f9e6e4',
                    200: '#f4d7d4',
                    300: '#e5b8b3',
                    400: '#d3938d',
                    500: '#b76e79',
                    600: '#a35460',
                    700: '#88414b',
                    800: '#713840',
                    900: '#5e3238',
                    950: '#34171b',
                },
                gold: {
                    50:  '#fefce8',
                    100: '#fef9c3',
                    200: '#fef08a',
                    300: '#fde047',
                    400: '#facc15',
                    500: '#eab308',
                    600: '#ca8a04',
                    700: '#a16207',
                    800: '#854d0e',
                    900: '#713f12',
                    950: '#422006',
                },
            },
            boxShadow: {
                'soft': '0 2px 12px -2px rgba(28,27,27,0.06)',
                'card': '0 8px 32px -12px rgba(28,27,27,0.12)',
                'glow': '0 0 30px rgba(183,110,121,0.25)',
                'hero': '0 40px 80px -40px rgba(244,215,212,0.6)',
            },
            borderRadius: { 
                'eight': '8px',
                '2xl': '16px',
                '3xl': '24px',
            },
            backgroundImage: {
                'gradient-brand': 'linear-gradient(135deg, #B76E79 0%, #F4D7D4 100%)',
                'gradient-soft':  'linear-gradient(135deg, #FBF9F8 0%, #F6F3F2 100%)',
                'gradient-hero':  'linear-gradient(135deg, #1C1B1B 0%, #49454F 100%)',
            },
            animation: {
                'float': 'float 6s ease-in-out infinite',
                'float-slow': 'float 8s ease-in-out infinite',
                'fade-up': 'fadeUp 0.5s ease-out forwards',
            },
            keyframes: {
                float: {
                    '0%, 100%': { transform: 'translateY(0px)' },
                    '50%':      { transform: 'translateY(-15px)' },
                },
                fadeUp: {
                    '0%':   { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
        },
    },

    plugins: [forms],
};
