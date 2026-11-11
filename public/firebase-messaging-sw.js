importScripts('https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging-compat.js');

// Public Firebase Web App credentials (client-side configuration)
const firebaseConfig = {
  apiKey: "AIzaSyA5xbihnIzCgvFLpbiczD6t2RITojmRFi8",
  authDomain: "booknailglam.firebaseapp.com",
  projectId: "booknailglam",
  storageBucket: "booknailglam.firebasestorage.app",
  messagingSenderId: "992288884013",
  appId: "1:992288884013:web:0b5739b63fab6f0c267fa4"
};

firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

// Handle background push messages (when browser tab is not active or Chrome is in background)
messaging.onBackgroundMessage((payload) => {
  const title = payload.notification?.title || payload.data?.title || 'Booknail Glam Studio';
  const body = payload.notification?.body || payload.data?.body || 'Ada pembaruan status booking Anda.';
  const clickUrl = payload.data?.url || payload.data?.click_action || '/dashboard';
  const bookingCode = payload.data?.booking_code || '';

  const notificationOptions = {
    body: body,
    icon: '/favicon.ico',
    badge: '/favicon.ico',
    tag: bookingCode || 'booknail-booking-' + Date.now(),
    renotify: true,
    requireInteraction: true, // Keep notification visible on screen until user interacts
    vibrate: [200, 100, 200, 100, 200],
    data: {
      url: clickUrl,
      booking_code: bookingCode,
    },
    actions: [
      { action: 'open', title: '👀 Lihat Detail' },
      { action: 'close', title: 'Tutup' }
    ]
  };

  return self.registration.showNotification(title, notificationOptions);
});

// Also listen for raw push events as fallback
self.addEventListener('push', (event) => {
  if (!event.data) return;

  try {
    const raw = event.data.json();
    const title = raw.notification?.title || raw.data?.title;
    if (!title) return; // onBackgroundMessage will handle standard FCM payloads

    const body = raw.notification?.body || raw.data?.body || '';
    const clickUrl = raw.data?.url || raw.data?.click_action || '/dashboard';
    const bookingCode = raw.data?.booking_code || '';

    event.waitUntil(
      self.registration.showNotification(title, {
        body: body,
        icon: '/favicon.ico',
        badge: '/favicon.ico',
        tag: bookingCode || 'booknail-booking-' + Date.now(),
        requireInteraction: true,
        data: { url: clickUrl, booking_code: bookingCode },
        actions: [
          { action: 'open', title: '👀 Lihat Detail' },
          { action: 'close', title: 'Tutup' }
        ]
      })
    );
  } catch (e) {
    // Non-JSON push payload
  }
});

// Handle Chrome notification click
self.addEventListener('notificationclick', (event) => {
  event.notification.close();

  if (event.action === 'close') {
    return;
  }

  const targetUrl = event.notification.data?.url || '/dashboard';

  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then((windowClients) => {
      // Focus existing tab if open
      for (const client of windowClients) {
        if ('focus' in client) {
          client.navigate(targetUrl);
          return client.focus();
        }
      }
      // If no window is open, open a new window
      if (clients.openWindow) {
        return clients.openWindow(targetUrl);
      }
    })
  );
});