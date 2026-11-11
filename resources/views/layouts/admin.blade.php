<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Admin') — {{ config('app.name', 'Nail Glam Studio') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;0,800;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-fcm-scripts />
</head>
<body class="bg-[#FBF9F8] text-[#1C1B1B] font-sans antialiased min-h-screen flex">

<aside id="sidebar" class="fixed left-0 top-0 z-40 h-screen w-64 bg-white text-[#1C1B1B] border-r border-[#DCD9D9] transition-transform -translate-x-full sm:translate-x-0">
    <div class="h-full flex flex-col">
        <div class="px-6 py-6 border-b border-[#DCD9D9]/60 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-[#B76E79] flex items-center justify-center text-white font-serif font-bold text-sm">
                    <x-icon name="sparkles" class="w-4 h-4" />
                </div>
                <span class="font-serif text-lg font-bold text-[#1C1B1B]">Nail Glam <span class="text-xs font-sans font-normal text-[#49454F]">Studio</span></span>
            </a>
            <span class="text-[10px] bg-[#F4D7D4]/60 text-[#B76E79] px-2 py-0.5 rounded-full font-bold uppercase tracking-wider">Admin</span>
        </div>
        <div class="px-6 py-4 border-b border-[#DCD9D9]/40 bg-[#FBF9F8]">
            <div class="text-[10px] uppercase tracking-widest text-[#49454F] font-bold">PANEL ADMIN</div>
            <div class="text-sm font-serif font-bold text-[#1C1B1B] mt-0.5">Nail Glam Studio</div>
        </div>
        <nav class="flex-1 p-4 space-y-1.5 overflow-y-auto">
            @php
                $r = request()->route()->getName();
                $nav = [
                    ['dashboard', 'Dashboard', 'layout-dashboard', 'OVERVIEW'],
                    ['admin.walk-in.create', 'Input Walk-In', 'store', 'MANAJEMEN'],
                    ['admin.bookings.index', 'Reservasi', 'calendar-check', 'MANAJEMEN'],
                    ['admin.appointments.index', 'Jadwal Mingguan', 'calendar-days', 'MANAJEMEN'],
                    ['admin.reports.index', 'Laporan & Ekspor', 'file-text', 'MANAJEMEN'],
                    ['admin.portfolios.index', 'Galeri Portofolio', 'images', 'MANAJEMEN'],
                    ['admin.services.index', 'Layanan', 'sparkles', 'MASTER DATA'],
                    ['admin.categories.index', 'Kategori', 'folder-tree', 'MASTER DATA'],
                    ['admin.artists.index', 'Teknisi / Artist', 'users', 'TIM'],
                    ['admin.schedules.index', 'Jadwal', 'clock', 'TIM'],
                    ['admin.leaves.index', 'Izin Artist', 'palmtree', 'TIM'],
                ];
            @endphp
            @foreach($nav as [$name, $label, $icon, $group])
                @php
                    static $prevGroup = '';
                    if ($group !== $prevGroup) { 
                        echo '<div class="mt-5 mb-2 px-3 text-[10px] uppercase font-bold text-[#49454F]/70 tracking-widest first:mt-0">'.$group.'</div>'; 
                        $prevGroup = $group; 
                    }
                    $parts = explode('.', $name);
                    $prefix = count($parts) >= 2 ? $parts[0].'.'.$parts[1] : $name;
                    $isActive = $r ? str_starts_with((string) $r, $prefix) : false;
                @endphp
                <a href="{{ Route::has($name) ? route($name) : '#' }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ $isActive ? 'bg-[#F4D7D4]/50 text-[#B76E79] font-bold shadow-soft' : 'text-[#49454F] hover:text-[#1C1B1B] hover:bg-[#F6F3F2]' }}">
                    <x-icon name="{{ $icon }}" class="w-4 h-4" />
                    <span class="flex-1">{{ $label }}</span>
                </a>
            @endforeach
        </nav>
        <div class="p-4 border-t border-[#DCD9D9]/60">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-full border border-[#DCD9D9] text-xs font-semibold text-[#49454F] hover:bg-[#F6F3F2] hover:text-[#1C1B1B] transition">
                    <x-icon name="log-out" class="w-3.5 h-3.5" />
                    Keluar
                </button>
            </form>
        </div>
    </div>
</aside>

<div class="flex-1 sm:ml-64 flex flex-col min-h-screen">
    <header class="sticky top-0 z-30 bg-[#FBF9F8]/90 backdrop-blur-md border-b border-[#DCD9D9]/60">
        <div class="px-6 sm:px-10 py-4 flex items-center justify-between">
            <div>
                <h1 class="font-serif text-2xl font-bold text-[#1C1B1B]">@yield('pageTitle', 'Dashboard')</h1>
                <p class="text-xs text-[#49454F] mt-0.5">@yield('pageSubtitle', 'Web-Based Reservation System')</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="hidden md:flex items-center gap-2">
                    <button type="button" id="fcm-status-badge" onclick="window.__requestFcmPermission && window.__requestFcmPermission()" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-stone-100 border border-stone-200 text-xs font-semibold text-stone-600 transition" title="Klik untuk mengaktifkan notifikasi Chrome">
                        <span class="w-2 h-2 rounded-full bg-stone-400"></span>
                        <span>Cek Notifikasi</span>
                    </button>
                    <button type="button" onclick="testChromeNotification(this)" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border border-[#B76E79]/40 bg-[#F4D7D4]/30 hover:bg-[#F4D7D4]/60 text-[#B76E79] text-xs font-semibold transition" title="Kirim notifikasi pop-up uji coba ke Chrome">
                        <x-icon name="bell" class="w-3.5 h-3.5" />
                        <span>Tes Pop-up</span>
                    </button>
                </div>
                <a href="{{ route('home') }}" class="text-xs font-semibold text-[#49454F] hover:text-[#B76E79] hidden sm:block flex items-center gap-1">
                    Lihat Website <x-icon name="external-link" class="w-3.5 h-3.5" />
                </a>
                <div class="flex items-center gap-3 pl-3 border-l border-[#DCD9D9]">
                    <span class="text-xs text-[#49454F]">Halo, <strong class="text-[#1C1B1B]">{{ Auth::user()->name }}</strong></span>
                    <div class="w-9 h-9 rounded-full bg-[#F4D7D4] border border-[#B76E79]/30 flex items-center justify-center text-[#B76E79] font-serif font-bold text-sm">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1 p-6 sm:p-10">
        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl border border-emerald-200 bg-emerald-50 text-emerald-800 text-sm font-semibold shadow-soft flex items-center gap-2">
                <x-icon name="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0" />
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('warning'))
            <div class="mb-6 p-4 rounded-2xl border border-amber-200 bg-amber-50 text-amber-800 text-sm font-semibold shadow-soft flex items-center gap-2">
                <x-icon name="alert-triangle" class="w-5 h-5 text-amber-600 shrink-0" />
                <span>{{ session('warning') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="mb-6 space-y-2">
                @foreach($errors->all() as $err)
                    <div class="p-4 rounded-2xl border border-rose-200 bg-rose-50 text-rose-800 text-sm font-semibold shadow-soft flex items-center gap-2">
                        <x-icon name="alert-circle" class="w-5 h-5 text-rose-600 shrink-0" />
                        <span>{{ $err }}</span>
                    </div>
                @endforeach
            </div>
        @endif
        @yield('content')
    </main>

    <footer class="border-t border-[#DCD9D9]/60 bg-[#FBF9F8] px-10 py-5 text-center text-xs text-[#49454F]">
        © {{ date('Y') }} Nail Glam Studio · Reservation System
    </footer>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.lucide) lucide.createIcons();
    });

    // Immediate Chrome pop-up and sound trigger
    function testChromeNotification(btn) {
        if (!('Notification' in window)) {
            alert('Browser ini tidak mendukung desktop notification.');
            return;
        }

        if (Notification.permission === 'granted') {
            triggerBookingPopup(
                '🔔 Tes Pop-Up Booking Masuk!',
                'Customer Aurelia baru saja booking Nail Art Glam (BK-7890). Klik untuk konfirmasi status booking.',
                '{{ route("admin.bookings.index") }}',
                'BK-7890'
            );
        } else if (Notification.permission === 'default') {
            Notification.requestPermission().then(permission => {
                updateNotificationUi(permission);
                if (permission === 'granted') {
                    triggerBookingPopup(
                        '🔔 Tes Pop-Up Booking Masuk!',
                        'Customer Aurelia baru saja booking Nail Art Glam (BK-7890). Klik untuk konfirmasi status booking.',
                        '{{ route("admin.bookings.index") }}',
                        'BK-7890'
                    );
                } else {
                    alert('Izin notifikasi tidak diberikan di Chrome.');
                }
            });
        } else {
            alert('Notifikasi diblokir di setelan Chrome. Silakan klik ikon gembok / setelan di sebelah kiri address bar https://booknail.test, ubah Notifications menjadi "Allow", lalu refresh halaman.');
        }
    }

    function playAudioChime() {
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
            playTone(587.33, 0.0, 0.35); // D5
            playTone(880.00, 0.15, 0.55); // A5
        } catch (e) {
            console.debug('Audio error:', e);
        }
    }

    function showBookingModal(title, body, url, bookingCode) {
        document.querySelectorAll('.fcm-booking-modal').forEach(el => el.remove());

        const modal = document.createElement('div');
        modal.className = 'fcm-booking-modal';
        modal.style.cssText = 'position:fixed;bottom:2rem;right:2rem;z-index:999999;max-width:440px;width:calc(100vw - 4rem);font-family:system-ui,sans-serif;animation:fcmSlideUp 0.35s ease-out;';
        
        const confirmUrl = url || (bookingCode ? `/admin/bookings?q=${encodeURIComponent(bookingCode)}` : '/admin/bookings');

        modal.innerHTML = `
            <div style="background:#fff;border-radius:1.25rem;border:2px solid #B76E79;box-shadow:0 20px 45px -10px rgba(183,110,121,0.3);overflow:hidden;">
                <div style="display:flex;align-items:center;gap:0.75rem;padding:1.25rem 1.25rem 0.75rem;border-bottom:1px solid #f3f0ef;">
                    <div style="width:40px;height:40px;border-radius:50%;background:#F4D7D4;display:flex;align-items:center;justify-content:center;font-size:1.25rem;">🔔</div>
                    <div style="flex:1;">
                        <span style="display:inline-block;font-size:0.65rem;font-weight:800;letter-spacing:0.08em;color:#B76E79;background:#FBF4F5;padding:0.15rem 0.5rem;border-radius:9999px;">BOOKING BARU</span>
                        <h3 style="font-size:0.95rem;font-weight:700;color:#1C1B1B;margin:2px 0 0 0;">${title}</h3>
                    </div>
                    <button type="button" onclick="this.closest('.fcm-booking-modal').remove()" style="background:none;border:none;font-size:1.5rem;color:#8C8889;cursor:pointer;padding:0.2rem;">&times;</button>
                </div>
                <div style="padding:1rem 1.25rem;font-size:0.875rem;color:#49454F;line-height:1.5;">
                    <p style="margin:0 0 0.75rem 0;">${body}</p>
                    ${bookingCode ? `<div style="display:inline-block;padding:0.35rem 0.75rem;background:#FBF9F8;border:1px dashed #DCD9D9;border-radius:0.5rem;font-size:0.8rem;color:#1C1B1B;">Kode Booking: <strong>${bookingCode}</strong></div>` : ''}
                </div>
                <div style="display:flex;align-items:center;justify-content:flex-end;gap:0.75rem;padding:0.85rem 1.25rem 1.25rem;background:#FAF8F8;">
                    <button type="button" onclick="this.closest('.fcm-booking-modal').remove()" style="background:transparent;border:1px solid #DCD9D9;color:#49454F;padding:0.55rem 1rem;border-radius:9999px;font-size:0.825rem;font-weight:600;cursor:pointer;">Tutup</button>
                    <a href="${confirmUrl}" style="background:#B76E79;color:#fff;text-decoration:none;padding:0.55rem 1.25rem;border-radius:9999px;font-size:0.825rem;font-weight:700;box-shadow:0 2px 8px rgba(183,110,121,0.35);">👉 Konfirmasi Status Booking</a>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
    }

    function triggerBookingPopup(title, body, url, bookingCode) {
        // 1. Play crystal audio chime
        playAudioChime();

        // 2. Chrome Native Desktop/OS notification
        if ('Notification' in window && Notification.permission === 'granted') {
            try {
                const n = new Notification(title, {
                    body: body,
                    icon: '/favicon.ico',
                    badge: '/favicon.ico',
                    tag: bookingCode || 'booknail-tag-' + Date.now(),
                    requireInteraction: true,
                });
                n.onclick = function() {
                    window.focus();
                    if (url) window.location.href = url;
                    this.close();
                };
            } catch (err) {
                console.warn('[Notif] Desktop error:', err);
            }
        }

        // 3. Interactive In-App Confirmation Modal
        showBookingModal(title, body, url, bookingCode);
    }

    function updateNotificationUi(permission) {
        const badge = document.getElementById('fcm-status-badge');
        if (!badge) return;
        if (permission === 'granted') {
            badge.innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span><span class="text-xs text-emerald-700 font-semibold">Notifikasi Chrome Aktif</span>';
            badge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200';
        } else if (permission === 'denied') {
            badge.innerHTML = '<span class="w-2 h-2 rounded-full bg-rose-500"></span><span class="text-xs text-rose-700 font-semibold">Notifikasi Diblokir</span>';
            badge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 border border-rose-200 cursor-pointer';
        } else {
            badge.innerHTML = '<span class="w-2 h-2 rounded-full bg-amber-500"></span><span class="text-xs text-amber-700 font-semibold">Aktifkan Notifikasi</span>';
            badge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 border border-amber-200 cursor-pointer hover:bg-amber-100 transition';
        }
    }

    // Real-time booking poller: checks for new bookings every 4 seconds
    let lastCheckedTime = new Date().toISOString();
    const seenBookingCodes = new Set();

    function pollNewBookings() {
        fetch('{{ route("admin.bookings.checkNew") }}?since=' + encodeURIComponent(lastCheckedTime), {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.server_time) {
                lastCheckedTime = data.server_time;
            }
            if (data.has_new && data.booking) {
                const b = data.booking;
                if (!seenBookingCodes.has(b.booking_code)) {
                    seenBookingCodes.add(b.booking_code);
                    triggerBookingPopup(
                        '📝 Booking Baru Masuk! Butuh Konfirmasi',
                        `${b.customer_name} melakukan booking baru (${b.booking_code}). Klik untuk konfirmasi status booking.`,
                        b.url,
                        b.booking_code
                    );
                }
            }
        })
        .catch(e => console.debug('Polling check:', e));
    }

    document.addEventListener('DOMContentLoaded', function() {
        if ('Notification' in window) {
            updateNotificationUi(Notification.permission);
        }
        setInterval(pollNewBookings, 4000);
    });
</script>
</body>
</html>
