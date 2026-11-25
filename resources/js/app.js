import './bootstrap';

import Alpine from 'alpinejs';
import QRCode from 'qrcode';

window.Alpine = Alpine;

window.renderLocalQRCodes = async (root = document) => {
    const targets = root.querySelectorAll('img[data-qr-value]');
    for (const img of targets) {
        const value = (img.getAttribute('data-qr-value') || '').trim();
        if (!value || img.dataset.renderedQr === value) continue;
        try {
            const dataUrl = await QRCode.toDataURL(value, {
                width: 240,
                margin: 1,
                errorCorrectionLevel: 'M',
                color: { dark: '#0f172a', light: '#ffffff' },
            });
            img.src = dataUrl;
            img.dataset.renderedQr = value;
        } catch (err) {
            console.error('Gagal membuat QR lokal:', err);
        }
    }
};

document.addEventListener('DOMContentLoaded', () => {
    window.renderLocalQRCodes();
    const observer = new MutationObserver(() => window.renderLocalQRCodes());
    observer.observe(document.body, { childList: true, subtree: true, attributes: true, attributeFilter: ['data-qr-value'] });
});

Alpine.start();
