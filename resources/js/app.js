import Alpine from 'alpinejs';
import * as lucideIcons from 'lucide';

window.Alpine = Alpine;

// Fallback init using npm package or CDN lucide instance
const initIcons = () => {
    try {
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        } else if (typeof lucideIcons.createIcons === 'function') {
            lucideIcons.createIcons({ icons: lucideIcons.icons || lucideIcons });
        }
    } catch (e) {
        console.error('Lucide icons render error:', e);
    }
};

document.addEventListener('DOMContentLoaded', () => {
    initIcons();
    // Run again after short delay to catch dynamic elements
    setTimeout(initIcons, 100);
});

window.refreshLucideIcons = initIcons;

Alpine.start();
