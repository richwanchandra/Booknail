/**
 * fcm-init.js
 * Firebase Cloud Messaging initialization, permission handling, and foreground notification popup.
 */

function getFcmConfig() {
  const get = (name) => {
    const el = document.querySelector(`meta[name="${name}"]`);
    return el ? el.getAttribute('content') : '';
  };
  return {
    apiKey:            get('fcm-api-key'),
    authDomain:        get('fcm-auth-domain'),
    projectId:         get('fcm-project-id'),
    storageBucket:     get('fcm-storage-bucket'),
    messagingSenderId: get('fcm-messaging-sender-id'),
    appId:             get('fcm-app-id'),
  };
}

function getCsrfToken() {
  const el = document.querySelector('meta[name="csrf-token"]');
  return el ? el.getAttribute('content') : '';
}

// Play notification sound chime (synthesized Web Audio API - no external file needed)
function playChimeSound() {
  try {
    const AudioCtx = window.AudioContext || window.webkitAudioContext;
    if (!AudioCtx) return;
    const ctx = new AudioCtx();
    
    const playTone = (freq, start, duration) => {
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.type = 'sine';
      osc.frequency.setValueAtTime(freq, ctx.currentTime + start);
      gain.gain.setValueAtTime(0, ctx.currentTime + start);
      gain.gain.linearRampToValueAtTime(0.25, ctx.currentTime + start + 0.03);
      gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + start + duration);
      osc.connect(gain);
      gain.connect(ctx.destination);
      osc.start(ctx.currentTime + start);
      osc.stop(ctx.currentTime + start + duration);
    };

    // Ding - Dong double chime
    playTone(587.33, 0.0, 0.35); // D5
    playTone(880.00, 0.15, 0.55); // A5
  } catch (e) {
    console.debug('[FCM] Audio chime suppressed by browser policy:', e);
  }
}

async function registerFcmToken(swReg, messaging, vapidKey) {
  try {
    const { getToken } = await import('https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging.js');
    const token = await getToken(messaging, {
      vapidKey: vapidKey || undefined,
      serviceWorkerRegistration: swReg,
    });

    if (token) {
      await fetch('/api/fcm-token', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken(),
          'Accept': 'application/json',
        },
        body: JSON.stringify({ token }),
      });
      console.info('[FCM] Token successfully registered with backend.');
      
      // Update UI if notification badge exists
      updateNotificationUiState('granted');
    }
  } catch (err) {
    console.warn('[FCM] Error obtaining/saving token:', err);
  }
}

function updateNotificationUiState(permission) {
  const badge = document.getElementById('fcm-status-badge');
  if (badge) {
    if (permission === 'granted') {
      badge.innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span><span class="text-xs text-emerald-700 font-semibold">Notifikasi Chrome Aktif</span>';
      badge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200';
    } else {
      badge.innerHTML = '<span class="w-2 h-2 rounded-full bg-amber-500"></span><span class="text-xs text-amber-700 font-semibold">Aktifkan Notifikasi</span>';
      badge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 border border-amber-200 cursor-pointer hover:bg-amber-100 transition';
    }
  }

  // Remove banner if permission granted
  if (permission === 'granted') {
    document.getElementById('fcm-permission-banner')?.remove();
  }
}

function showPermissionBanner(onRequestPermission) {
  if (document.getElementById('fcm-permission-banner')) return;
  if (Notification.permission === 'granted' || Notification.permission === 'denied') return;

  const isAdmin = window.location.pathname.startsWith('/admin');
  const bannerTitle = isAdmin 
    ? 'Aktifkan Notifikasi Booking di Chrome' 
    : '🔔 Aktifkan Notifikasi Status Booking di Chrome';
  const bannerDesc = isAdmin
    ? 'Dapatkan pop-up instan di Chrome setiap kali customer melakukan booking baru agar bisa langsung dikonfirmasi statusnya.'
    : 'Dapatkan pemberitahuan langsung di Chrome saat DP Anda diverifikasi, jadwal dikonfirmasi, dan reminder perawatan!';

  const banner = document.createElement('div');
  banner.id = 'fcm-permission-banner';
  banner.className = 'fcm-banner';
  banner.innerHTML = `
    <div class="fcm-banner__content">
      <div class="fcm-banner__icon">🔔</div>
      <div>
        <div class="fcm-banner__title">${bannerTitle}</div>
        <div class="fcm-banner__desc">${bannerDesc}</div>
      </div>
    </div>
    <div class="fcm-banner__actions">
      <button id="fcm-banner-allow-btn" class="fcm-banner__btn-allow">Aktifkan Sekarang</button>
      <button id="fcm-banner-close-btn" class="fcm-banner__btn-close" title="Nanti saja">&times;</button>
    </div>
  `;

  // Inject banner styles
  if (!document.getElementById('fcm-banner-styles')) {
    const style = document.createElement('style');
    style.id = 'fcm-banner-styles';
    style.textContent = `
      .fcm-banner {
        position: sticky;
        top: 0;
        z-index: 50;
        background: linear-gradient(135deg, #1C1B1B 0%, #2D272A 100%);
        border-bottom: 2px solid #B76E79;
        color: #fff;
        padding: 0.75rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        animation: fcmSlideDown 0.3s ease-out;
      }
      @keyframes fcmSlideDown {
        from { transform: translateY(-100%); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
      }
      .fcm-banner__content {
        display: flex;
        align-items: center;
        gap: 0.85rem;
      }
      .fcm-banner__icon {
        font-size: 1.5rem;
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
      }
      .fcm-banner__title {
        font-weight: 700;
        font-size: 0.9rem;
        color: #F4D7D4;
        letter-spacing: 0.01em;
      }
      .fcm-banner__desc {
        font-size: 0.75rem;
        color: #DCD9D9;
      }
      .fcm-banner__actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-shrink: 0;
      }
      .fcm-banner__btn-allow {
        background: #B76E79;
        color: #fff;
        border: none;
        padding: 0.45rem 1rem;
        border-radius: 9999px;
        font-size: 0.8rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 2px 8px rgba(183,110,121,0.4);
      }
      .fcm-banner__btn-allow:hover {
        background: #a35d68;
        transform: translateY(-1px);
      }
      .fcm-banner__btn-close {
        background: none;
        border: none;
        color: #999;
        font-size: 1.4rem;
        line-height: 1;
        cursor: pointer;
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
      }
      .fcm-banner__btn-close:hover {
        color: #fff;
      }
    `;
    document.head.appendChild(style);
  }

  // Insert banner before main or as first child of body
  const targetHeader = document.querySelector('header') || document.body;
  targetHeader.parentNode.insertBefore(banner, targetHeader);

  document.getElementById('fcm-banner-allow-btn')?.addEventListener('click', onRequestPermission);
  document.getElementById('fcm-banner-close-btn')?.addEventListener('click', () => banner.remove());
}

/**
 * Show a prominent popup modal / toast for the admin to confirm booking
 */
function showFcmBookingPopup(title, body, url, bookingCode) {
  playChimeSound();

  // Try showing native Chrome notification if permitted
  if (Notification.permission === 'granted') {
    try {
      const nativeNotif = new Notification(title, {
        body: body,
        icon: '/favicon.ico',
        badge: '/favicon.ico',
        tag: bookingCode || 'booknail-booking-' + Date.now(),
        data: { url: url },
      });
      nativeNotif.onclick = function() {
        window.focus();
        if (url) window.location.href = url;
        this.close();
      };
    } catch (e) {
      console.debug('[FCM] Native notification fallback:', e);
    }
  }

  // Remove existing modals
  document.querySelectorAll('.fcm-booking-modal').forEach(el => el.remove());

  const modal = document.createElement('div');
  modal.className = 'fcm-booking-modal';
  modal.setAttribute('role', 'dialog');
  modal.setAttribute('aria-modal', 'true');
  
  const isAdmin = window.location.pathname.startsWith('/admin');
  const confirmUrl = url || (isAdmin ? (bookingCode ? `/admin/bookings?q=${encodeURIComponent(bookingCode)}` : '/admin/bookings') : '/dashboard');
  const badgeText = isAdmin ? 'BOOKING BARU' : 'UPDATE BOOKING';
  const buttonText = isAdmin ? '👉 Konfirmasi Status Booking' : '👀 Lihat Detail Booking';

  modal.innerHTML = `
    <div class="fcm-modal__card">
      <div class="fcm-modal__header">
        <div class="fcm-modal__icon-wrap">
          <span class="fcm-modal__bell">🔔</span>
        </div>
        <div class="fcm-modal__header-text">
          <span class="fcm-modal__badge">${badgeText}</span>
          <h3 class="fcm-modal__title">${escapeHtml(title)}</h3>
        </div>
        <button class="fcm-modal__close" aria-label="Tutup">&times;</button>
      </div>

      <div class="fcm-modal__body">
        <p class="fcm-modal__message">${escapeHtml(body)}</p>
        ${bookingCode ? `<div class="fcm-modal__code-chip">Kode Booking: <strong>${escapeHtml(bookingCode)}</strong></div>` : ''}
      </div>

      <div class="fcm-modal__footer">
        <button class="fcm-modal__btn-dismiss">Tutup</button>
        <a href="${confirmUrl}" class="fcm-modal__btn-confirm">
          ${buttonText}
        </a>
      </div>
    </div>
  `;

  // Inject popup modal styles
  if (!document.getElementById('fcm-modal-styles')) {
    const style = document.createElement('style');
    style.id = 'fcm-modal-styles';
    style.textContent = `
      .fcm-booking-modal {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        z-index: 999999;
        max-width: 440px;
        width: calc(100vw - 4rem);
        animation: fcmPopIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) both;
        font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
      }
      @keyframes fcmPopIn {
        from { transform: translateY(40px) scale(0.92); opacity: 0; }
        to { transform: translateY(0) scale(1); opacity: 1; }
      }
      .fcm-modal__card {
        background: #FFFFFF;
        border-radius: 1.25rem;
        border: 2px solid #B76E79;
        box-shadow: 0 20px 45px -10px rgba(183,110,121,0.25), 0 0 0 1px rgba(0,0,0,0.05);
        overflow: hidden;
      }
      .fcm-modal__header {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        padding: 1.25rem 1.25rem 0.75rem 1.25rem;
        border-bottom: 1px solid #F6F3F2;
      }
      .fcm-modal__icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: #F4D7D4;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
        animation: fcmRing 1.2s infinite ease-in-out;
      }
      @keyframes fcmRing {
        0%, 100% { transform: rotate(0); }
        20% { transform: rotate(15deg); }
        40% { transform: rotate(-15deg); }
        60% { transform: rotate(10deg); }
        80% { transform: rotate(-5deg); }
      }
      .fcm-modal__header-text { flex: 1; min-width: 0; }
      .fcm-modal__badge {
        display: inline-block;
        font-size: 0.65rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #B76E79;
        background: #FBF4F5;
        padding: 0.15rem 0.5rem;
        border-radius: 9999px;
        margin-bottom: 0.25rem;
      }
      .fcm-modal__title {
        font-size: 0.98rem;
        font-weight: 700;
        color: #1C1B1B;
        margin: 0;
        line-height: 1.3;
      }
      .fcm-modal__close {
        background: none;
        border: none;
        font-size: 1.5rem;
        line-height: 1;
        color: #8C8889;
        cursor: pointer;
        padding: 0.2rem;
      }
      .fcm-modal__close:hover { color: #1C1B1B; }
      .fcm-modal__body {
        padding: 1rem 1.25rem;
        font-size: 0.875rem;
        color: #49454F;
        line-height: 1.5;
      }
      .fcm-modal__message { margin: 0 0 0.75rem 0; }
      .fcm-modal__code-chip {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        background: #FBF9F8;
        border: 1px dashed #DCD9D9;
        border-radius: 0.5rem;
        font-size: 0.8rem;
        color: #1C1B1B;
      }
      .fcm-modal__footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.75rem;
        padding: 0.85rem 1.25rem 1.25rem 1.25rem;
        background: #FAF8F8;
      }
      .fcm-modal__btn-dismiss {
        background: transparent;
        border: 1px solid #DCD9D9;
        color: #49454F;
        padding: 0.55rem 1rem;
        border-radius: 9999px;
        font-size: 0.825rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
      }
      .fcm-modal__btn-dismiss:hover { background: #F0ECEB; color: #1C1B1B; }
      .fcm-modal__btn-confirm {
        background: #B76E79;
        color: #FFFFFF;
        text-decoration: none;
        padding: 0.55rem 1.25rem;
        border-radius: 9999px;
        font-size: 0.825rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        transition: all 0.2s;
        box-shadow: 0 2px 8px rgba(183,110,121,0.35);
      }
      .fcm-modal__btn-confirm:hover {
        background: #9f5b66;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(183,110,121,0.45);
      }
    `;
    document.head.appendChild(style);
  }

  document.body.appendChild(modal);

  const dismissModal = () => {
    modal.style.transition = 'all 0.3s ease-in';
    modal.style.opacity = '0';
    modal.style.transform = 'translateY(20px)';
    setTimeout(() => modal.remove(), 300);
  };

  modal.querySelector('.fcm-modal__close')?.addEventListener('click', dismissModal);
  modal.querySelector('.fcm-modal__btn-dismiss')?.addEventListener('click', dismissModal);
}

function escapeHtml(str) {
  return String(str ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}

async function initFcm() {
  const config = getFcmConfig();
  if (!config.projectId) return;

  // FCM requires HTTPS or localhost
  if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
    console.info('[FCM] Skipping: requires HTTPS or localhost.');
    return;
  }

  try {
    const { initializeApp, getApps } = await import('https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js');
    const { getMessaging, onMessage } = await import('https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging.js');

    const app = getApps().length ? getApps()[0] : initializeApp(config);
    const messaging = getMessaging(app);

    // Register service worker
    const swReg = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
    await navigator.serviceWorker.ready;

    const vapidKey = document.querySelector('meta[name="fcm-vapid-key"]')?.getAttribute('content') ?? undefined;

    const requestPermissionAndRegister = async () => {
      try {
        const permission = await Notification.requestPermission();
        updateNotificationUiState(permission);
        if (permission === 'granted') {
          await registerFcmToken(swReg, messaging, vapidKey);
        }
      } catch (err) {
        console.warn('[FCM] Request permission failed:', err);
      }
    };

    // If permission already granted, register/refresh token immediately
    if (Notification.permission === 'granted') {
      updateNotificationUiState('granted');
      await registerFcmToken(swReg, messaging, vapidKey);
    } else if (Notification.permission === 'default') {
      updateNotificationUiState('default');
      // Show admin permission banner for interactive user click
      showPermissionBanner(requestPermissionAndRegister);
    }

    // Expose request function globally for header button
    window.__requestFcmPermission = requestPermissionAndRegister;

    // Listen for foreground push messages
    onMessage(messaging, (payload) => {
      console.info('[FCM] Incoming foreground message:', payload);
      const title = payload.notification?.title || payload.data?.title || '📝 Booking Baru Masuk!';
      const body = payload.notification?.body || payload.data?.body || 'Customer baru saja melakukan booking.';
      const url = payload.data?.url || payload.data?.click_action || '/admin/bookings';
      const bookingCode = payload.data?.booking_code || '';

      showFcmBookingPopup(title, body, url, bookingCode);
    });

  } catch (err) {
    console.warn('[FCM] Init error:', err);
  }
}

// Initialise when user is logged in
if (document.querySelector('meta[name="user-id"]')) {
  if ('Notification' in window && 'serviceWorker' in navigator) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initFcm);
    } else {
      initFcm();
    }
  }
}

window.showFcmBookingPopup = showFcmBookingPopup;
window.playChimeSound = playChimeSound;

export { showFcmBookingPopup, playChimeSound };