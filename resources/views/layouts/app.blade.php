<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' — StatWeb' : 'StatWeb' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --sw-navy: #162032;
            --sw-gold: #F6C90E;
            --sw-bg: #F7F9FA;
            --sw-border: #E2E8F0;
            --sidebar-width: 256px;
        }
        body { font-family: 'Inter', sans-serif; background: var(--sw-bg); }
        .sw-display { font-family: 'Space Grotesk', sans-serif; }
        .sw-mono { font-family: 'JetBrains Mono', monospace; font-variant-numeric: tabular-nums; }

        html { -webkit-text-size-adjust: 100%; }

        /* HP/tablet: header menempel di atas, tingginya 56px; menu hamburger MELAYANG di bawahnya (tidak mendorong/menimpa konten) */
        .app-header {
            position: sticky;
            top: 0;
            z-index: 30;
        }

        /* Desktop: header tetap di samping sidebar agar tidak ikut scroll horizontal */
        @media (min-width: 1024px) {
            .app-header {
                position: fixed;
                top: 0;
                left: var(--sidebar-width);
                right: 0;
                z-index: 30;
                height: 56px;
            }
            .app-main {
                margin-top: 56px; /* tinggi header */
            }
        }
    </style>
</head>
<body class="antialiased">
<div class="min-h-screen flex">

    {{-- SIDEBAR (Desktop) --}}
    <aside class="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0" style="background: #162032; z-index: 40;">
        <div class="px-6 py-5 border-b border-white/10">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-md flex items-center justify-center sw-mono font-bold text-sm" style="background: #f8f8f6; color: #162032;">
                    Σ
                </div>
                <span class="sw-display font-semibold text-lg tracking-tight text-white">StatWeb</span>
            </a>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition"
               style="{{ request()->routeIs('dashboard') ? 'background: rgba(246,201,14,0.15); color: #F6C90E;' : 'color: rgba(255,255,255,0.6);' }}"
               onmouseover="if(!this.style.background.includes('F6C90E')) { this.style.color='rgba(255,255,255,0.9)'; this.style.background='rgba(255,255,255,0.05)'; }"
               onmouseout="if(!this.style.background.includes('F6C90E')) { this.style.color='rgba(255,255,255,0.6)'; this.style.background=''; }">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>
            <a href="{{ route('datasets.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition"
               style="{{ request()->routeIs('datasets.*') || request()->routeIs('analysis.*') ? 'background: rgba(246,201,14,0.15); color: #F6C90E;' : 'color: rgba(255,255,255,0.6);' }}"
               onmouseover="if(!this.style.background.includes('F6C90E')) { this.style.color='rgba(255,255,255,0.9)'; this.style.background='rgba(255,255,255,0.05)'; }"
               onmouseout="if(!this.style.background.includes('F6C90E')) { this.style.color='rgba(255,255,255,0.6)'; this.style.background=''; }">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                </svg>
                Dataset
            </a>
        </nav>

        <div class="px-3 py-4 border-t border-white/10">
            <div class="px-3 py-2 text-xs" style="color: rgba(255,255,255,0.4);">
                Masuk sebagai
                <div class="font-medium sw-mono truncate" style="color: rgba(255,255,255,0.7);">{{ Auth::user()->name }}</div>
            </div>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <div class="flex-1 min-w-0 lg:pl-64 flex flex-col min-h-screen">

        {{-- Latar gelap menu mobile: ketuk di luar menu untuk menutup --}}
        <div id="mobile-nav-backdrop" class="hidden lg:hidden fixed inset-0" style="z-index: 20; background: rgba(15, 23, 42, 0.35);"></div>

        {{-- TOP BAR --}}
        <header class="app-header bg-white border-b border-slate-200">
            <div class="flex items-center justify-between px-4 lg:px-8 h-14 lg:h-full">

                {{-- Mobile: logo + hamburger --}}
                <div class="flex items-center gap-3 lg:hidden">
                    <button id="mobile-nav-toggle" type="button"
                            aria-label="Buka menu navigasi" aria-controls="mobile-nav" aria-expanded="false"
                            class="p-2.5 -ml-2.5 text-slate-500 rounded-lg active:bg-slate-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded flex items-center justify-center sw-mono font-bold text-xs" style="background: #162032; color: #F6C90E;">Σ</div>
                        <span class="sw-display font-semibold text-slate-800">StatWeb</span>
                    </div>
                </div>

                {{-- Desktop: page title --}}
                <div class="hidden lg:block">
                    @isset($header)
                        <div class="sw-display font-semibold text-slate-800 text-lg">{{ $header }}</div>
                    @endisset
                </div>

                <div class="flex items-center gap-3">
                    <span class="hidden sm:inline text-xs text-slate-400 sw-mono">{{ Auth::user()->name }}</span>
                </div>
            </div>

            {{-- Mobile dropdown nav (melayang di bawah header) --}}
            <div id="mobile-nav" class="hidden lg:hidden absolute left-0 right-0 top-full bg-white border-b border-slate-200 shadow-lg px-4 py-3 space-y-1">
                <a href="{{ route('dashboard') }}"
                   class="block px-3 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-600' }}"
                   style="{{ request()->routeIs('dashboard') ? 'background: #0F2C59;' : '' }}">
                    Dashboard
                </a>
                <a href="{{ route('datasets.index') }}"
                   class="block px-3 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('datasets.*') || request()->routeIs('analysis.*') ? 'text-white' : 'text-slate-600' }}"
                   style="{{ request()->routeIs('datasets.*') || request()->routeIs('analysis.*') ? 'background: #0F2C59;' : '' }}">
                    Dataset
                </a>
                <div class="px-3 pt-3 mt-2 border-t border-slate-100 text-xs text-slate-400">
                    Masuk sebagai
                    <span class="sw-mono text-slate-500 break-all">{{ Auth::user()->name }}</span>
                </div>
            </div>
        </header>

        {{-- Mobile page header --}}
        @isset($header)
            <div class="lg:hidden px-4 pt-4">
                <div class="sw-display font-semibold text-slate-800 text-lg">{{ $header }}</div>
            </div>
        @endisset

        {{-- PAGE CONTENT --}}
        <main class="app-main flex-1 px-4 py-6 lg:px-8 lg:py-8" style="padding-bottom: max(1.5rem, env(safe-area-inset-bottom));">
            {{ $slot }}
        </main>

    </div>
</div>
<script>
    (function () {
        var btn = document.getElementById('mobile-nav-toggle');
        var nav = document.getElementById('mobile-nav');
        var backdrop = document.getElementById('mobile-nav-backdrop');
        if (!btn || !nav || !backdrop) return;

        function setOpen(open) {
            nav.classList.toggle('hidden', !open);
            backdrop.classList.toggle('hidden', !open);
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        }

        btn.addEventListener('click', function () { setOpen(nav.classList.contains('hidden')); });
        backdrop.addEventListener('click', function () { setOpen(false); });
        nav.addEventListener('click', function (e) { if (e.target.closest('a')) setOpen(false); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setOpen(false); });
        window.matchMedia('(min-width: 1024px)').addEventListener('change', function (e) { if (e.matches) setOpen(false); });
    })();
</script>
</body>
</html>
