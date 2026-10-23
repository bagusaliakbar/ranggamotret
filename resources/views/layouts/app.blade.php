<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'RanggaMotret.id — Photography Studio & Academy')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome (Fallback) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Phosphor Icons (Duotone, Regular, Fill, Bold) -->
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/regular/style.css"/>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/duotone/style.css"/>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/fill/style.css"/>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/bold/style.css"/>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        slate: {
                            950: '#020617',
                            900: '#0f172a',
                            800: '#1e293b',
                            700: '#334155',
                            600: '#475569',
                            500: '#64748b',
                            100: '#f1f5f9',
                            50:  '#f8fafc',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('head')

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
        }
        .clean-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }
        .clean-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.07);
        }
        .btn-dark {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .btn-dark:hover {
            background-color: #1e293b;
        }
        .btn-outline {
            background-color: #ffffff;
            color: #0f172a;
            border: 1px solid #cbd5e1;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .btn-outline:hover {
            background-color: #f8fafc;
            border-color: #94a3b8;
        }
    </style>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon-square.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}" type="image/png">
    @stack('styles')
</head>
<body class="flex flex-col min-h-screen bg-slate-50 text-slate-900 antialiased">

    <!-- Header Navigation (Clean White & Dark Grey Accent) -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                
                <!-- Brand Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('images/logo-icon.png') }}" alt="RanggaMotret.id" class="w-12 h-12 object-contain shrink-0 group-hover:scale-105 transition-transform duration-200">
                    <div class="flex flex-col">
                        <span class="font-extrabold text-xl tracking-tight text-slate-900">RANGGA<span class="text-slate-600">MOTRET.ID</span></span>
                        <span class="text-[9px] text-slate-500 font-semibold tracking-widest uppercase -mt-1">Photography & Academy</span>
                    </div>
                </a>

                <!-- Desktop Nav -->
                <nav class="hidden md:flex items-center gap-8 text-xs font-bold tracking-wider uppercase">
                    <a href="{{ route('home') }}" class="transition-colors hover:text-slate-900 {{ request()->routeIs('home') ? 'text-slate-900 border-b-2 border-slate-900 pb-1' : 'text-slate-600' }}">Beranda</a>
                    <a href="{{ route('public.services') }}" class="transition-colors hover:text-slate-900 {{ request()->routeIs('public.services*') ? 'text-slate-900 border-b-2 border-slate-900 pb-1' : 'text-slate-600' }}">Fotografi</a>
                    <a href="{{ route('public.portfolio') }}" class="transition-colors hover:text-slate-900 {{ request()->routeIs('public.portfolio') ? 'text-slate-900 border-b-2 border-slate-900 pb-1' : 'text-slate-600' }}">Portofolio</a>
                    <a href="{{ route('public.courses') }}" class="transition-colors hover:text-slate-900 {{ request()->routeIs('public.courses*') ? 'text-slate-900 border-b-2 border-slate-900 pb-1' : 'text-slate-600' }}">Kursus</a>
                    <a href="{{ route('public.consultation') }}" class="transition-colors hover:text-slate-900 {{ request()->routeIs('public.consultation') ? 'text-slate-900 border-b-2 border-slate-900 pb-1' : 'text-slate-600' }}">Konsultasi</a>
                </nav>

                <!-- Actions / Auth User Dropdown -->
                <div class="hidden md:flex items-center gap-4">
                    @auth
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('calendar') }}" class="p-2.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 hover:text-slate-900 hover:bg-slate-200 transition-colors" title="Jadwal Kalender">
                                <i class="ph-duotone ph-calendar-blank text-base"></i>
                            </a>
                        @endif

                        <!-- Notification Dropdown Preview -->
                        <div x-data="{ notifOpen: false }" class="relative">
                            <button @click="notifOpen = !notifOpen" 
                                    type="button"
                                    class="relative p-2.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 hover:text-slate-900 hover:bg-slate-200 transition-colors focus:outline-none focus:ring-2 focus:ring-slate-400" 
                                    title="Notifikasi">
                                <i class="ph-duotone ph-bell text-base"></i>
                                @if(Auth::user()->unreadNotificationsCount() > 0)
                                    <span class="absolute -top-1 -right-1 w-4 h-4 bg-rose-600 text-white text-[9px] font-black rounded-full flex items-center justify-center shadow-xs animate-pulse">
                                        {{ Auth::user()->unreadNotificationsCount() }}
                                    </span>
                                @endif
                            </button>

                            <!-- Preview Dropdown Panel -->
                            <div x-show="notifOpen" 
                                 @click.outside="notifOpen = false" 
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                                 class="absolute right-0 mt-2 w-80 sm:w-96 bg-white border border-slate-200 rounded-3xl shadow-2xl z-50 overflow-hidden"
                                 style="display: none;">
                                
                                <!-- Header Preview -->
                                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/80">
                                    <div class="flex items-center gap-2">
                                        <i class="ph-duotone ph-bell-ringing text-slate-800 text-base"></i>
                                        <h4 class="font-extrabold text-slate-900 text-xs tracking-tight">Pemberitahuan</h4>
                                        @if(Auth::user()->unreadNotificationsCount() > 0)
                                            <span class="px-2 py-0.5 rounded-full bg-rose-50 border border-rose-200 text-rose-700 text-[10px] font-black">
                                                {{ Auth::user()->unreadNotificationsCount() }} Baru
                                            </span>
                                        @endif
                                    </div>

                                    @if(Auth::user()->unreadNotificationsCount() > 0)
                                        <form action="{{ route('notifications.read-all') }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-[11px] font-bold text-slate-500 hover:text-slate-900 transition-colors">
                                                Tandai Semua Dibaca
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                <!-- Notification List Preview (Top 5) -->
                                @php
                                    $previewNotifsApp = Auth::user()->notifikasis()->latest()->take(5)->get();
                                @endphp

                                <div class="max-h-85 overflow-y-auto divide-y divide-slate-100 no-scrollbar">
                                    @forelse($previewNotifsApp as $n)
                                        <a href="{{ route('notifications.read', $n->id) }}" 
                                           class="p-4 block transition-colors hover:bg-slate-50 group {{ !$n->is_read ? 'bg-amber-50/40' : 'bg-white opacity-80' }}">
                                            <div class="flex items-start gap-3">
                                                <!-- Category Icon Avatar -->
                                                <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 text-sm {{ !$n->is_read ? 'bg-slate-900 text-emerald-400 shadow-2xs' : 'bg-slate-100 text-slate-500' }}">
                                                    @if(str_contains(strtolower($n->judul), 'pembayaran') || str_contains(strtolower($n->judul), 'transaksi'))
                                                        <i class="ph-duotone ph-receipt"></i>
                                                    @elseif(str_contains(strtolower($n->judul), 'booking') || str_contains(strtolower($n->judul), 'reservasi') || str_contains(strtolower($n->judul), 'jadwal'))
                                                        <i class="ph-duotone ph-calendar-check"></i>
                                                    @elseif(str_contains(strtolower($n->judul), 'kursus') || str_contains(strtolower($n->judul), 'materi'))
                                                        <i class="ph-duotone ph-graduation-cap"></i>
                                                    @else
                                                        <i class="ph-duotone ph-bell-simple"></i>
                                                    @endif
                                                </div>

                                                <!-- Content -->
                                                <div class="space-y-1 flex-1 min-w-0">
                                                    <div class="flex items-center justify-between gap-2">
                                                        <h5 class="text-xs font-black text-slate-900 truncate group-hover:text-black">
                                                            {{ $n->judul }}
                                                        </h5>
                                                        @if(!$n->is_read)
                                                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                                        @endif
                                                    </div>
                                                    <p class="text-[11px] text-slate-600 font-medium leading-relaxed line-clamp-2">
                                                        {{ $n->pesan }}
                                                    </p>
                                                    <div class="text-[10px] text-slate-400 font-semibold pt-0.5 flex items-center gap-1">
                                                        <i class="ph-duotone ph-clock text-[10px]"></i>
                                                        <span>{{ $n->created_at->diffForHumans() }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="p-8 text-center space-y-2">
                                            <div class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-lg mx-auto">
                                                <i class="ph-duotone ph-bell-slash"></i>
                                            </div>
                                            <p class="text-xs font-bold text-slate-700">Belum Ada Notifikasi</p>
                                            <p class="text-[11px] text-slate-400 font-medium">Pemberitahuan transaksi atau jadwal akan muncul di sini.</p>
                                        </div>
                                    @endforelse
                                </div>

                                <!-- Footer Link to Full Page -->
                                <div class="p-3 bg-slate-50 border-t border-slate-100 text-center">
                                    <a href="{{ route('notifications.index') }}" 
                                       class="text-xs font-extrabold text-slate-800 hover:text-black inline-flex items-center gap-1 transition-colors">
                                        <span>Lihat Semua Notifikasi</span>
                                        <i class="ph-duotone ph-arrow-right text-xs"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Menu -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center gap-3 px-3 py-2 rounded-xl bg-slate-100 border border-slate-200 text-left hover:bg-slate-200 transition-colors">
                                @if(Auth::user()->avatar)
                                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="w-7 h-7 rounded-lg object-cover ring-1 ring-slate-300">
                                @else
                                    <div class="w-7 h-7 rounded-lg bg-slate-900 text-white font-bold flex items-center justify-center text-xs">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="text-xs">
                                    <div class="font-bold text-slate-900 truncate max-w-25">{{ Auth::user()->name }}</div>
                                </div>
                                <i class="ph-bold ph-caret-down text-[10px] text-slate-500"></i>
                            </button>

                            <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 mt-2 w-56 bg-white border border-slate-200 rounded-xl shadow-xl py-2 z-50 text-xs">
                                <div class="px-4 py-2 border-b border-slate-100">
                                    <p class="font-bold text-slate-900 truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-slate-500 text-[10px] capitalize">{{ Auth::user()->role }}</p>
                                </div>

                                <a href="{{ route('dashboard') }}" class="block px-4 py-2.5 text-slate-900 font-bold hover:bg-slate-50">
                                    <i class="ph-duotone ph-gauge mr-2 text-slate-700 text-sm"></i> Dashboard
                                </a>
                                <div class="border-t border-slate-100 my-1"></div>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2.5 text-rose-600 hover:bg-rose-50 font-semibold flex items-center gap-2">
                                        <i class="ph-duotone ph-sign-out text-sm"></i> Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-xs font-bold text-slate-700 hover:text-slate-900 px-3 py-2">Masuk</a>
                        <a href="{{ route('register') }}" class="btn-dark px-5 py-2.5 rounded-xl text-xs font-bold shadow-md">Daftar</a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex md:hidden items-center gap-3" x-data="{ mobileOpen: false }">
                    <button @click="mobileOpen = !mobileOpen" class="p-2.5 rounded-xl bg-slate-100 text-slate-700 border border-slate-200">
                        <i class="ph-duotone ph-list text-lg"></i>
                    </button>

                    <div x-show="mobileOpen" @click.outside="mobileOpen = false" class="absolute top-20 left-0 right-0 bg-white border-b border-slate-200 p-6 flex flex-col gap-4 text-xs font-semibold z-50 shadow-xl">
                        <a href="{{ route('home') }}" class="text-slate-800">Beranda</a>
                        <a href="{{ route('public.services') }}" class="text-slate-800">Fotografi</a>
                        <a href="{{ route('public.portfolio') }}" class="text-slate-800">Portofolio</a>
                        <a href="{{ route('public.courses') }}" class="text-slate-800">Kursus Fotografi</a>
                        <a href="{{ route('public.consultation') }}" class="text-slate-800">Konsultasi</a>
                        @auth
                            <a href="{{ route('profile') }}" class="text-slate-900 font-bold">Profil Saya</a>
                        @else
                            <a href="{{ route('login') }}" class="text-slate-800">Masuk</a>
                            <a href="{{ route('register') }}" class="btn-dark text-center py-2.5 rounded-xl">Daftar Akun</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Floating Toast Notifications (Top-Right) -->
    @if(session('success') || session('error') || session('status'))
        <div x-data="{ show: true }" 
             x-init="setTimeout(() => show = false, 4500)"
             x-show="show" 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="-translate-y-4 opacity-0 scale-95"
             x-transition:enter-end="translate-y-0 opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-y-0 opacity-100 scale-100"
             x-transition:leave-end="-translate-y-4 opacity-0 scale-95"
             class="fixed top-24 right-4 sm:right-6 z-50 max-w-sm w-full shadow-2xl pointer-events-auto"
             style="display: none;">
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xl flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5 border border-emerald-100">
                            <i class="ph-duotone ph-check-circle text-lg"></i>
                        </div>
                        <div class="space-y-0.5">
                            <div class="text-xs font-extrabold text-slate-900">Berhasil</div>
                            <div class="text-xs text-slate-600 font-medium leading-relaxed">{{ session('success') }}</div>
                        </div>
                    </div>
                    <button @click="show = false" class="text-slate-400 hover:text-slate-700 p-1">
                        <i class="ph-bold ph-x text-sm"></i>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xl flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 mt-0.5 border border-rose-100">
                            <i class="ph-duotone ph-warning-circle text-lg"></i>
                        </div>
                        <div class="space-y-0.5">
                            <div class="text-xs font-extrabold text-slate-900">Pemberitahuan</div>
                            <div class="text-xs text-slate-600 font-medium leading-relaxed">{{ session('error') }}</div>
                        </div>
                    </div>
                    <button @click="show = false" class="text-slate-400 hover:text-slate-700 p-1">
                        <i class="ph-bold ph-x text-sm"></i>
                    </button>
                </div>
            @endif

            @if(session('status'))
                <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xl flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 mt-0.5 border border-blue-100">
                            <i class="ph-duotone ph-info text-lg"></i>
                        </div>
                        <div class="space-y-0.5">
                            <div class="text-xs font-extrabold text-slate-900">Informasi</div>
                            <div class="text-xs text-slate-600 font-medium leading-relaxed">{{ session('status') }}</div>
                        </div>
                    </div>
                    <button @click="show = false" class="text-slate-400 hover:text-slate-700 p-1">
                        <i class="ph-bold ph-x text-sm"></i>
                    </button>
                </div>
            @endif
        </div>
    @endif

    <!-- Content Area -->
    <main class="grow">
        @yield('content')
    </main>

    <!-- Footer (Deep Dark Grey Accent Footer) -->
    <footer class="bg-slate-900 text-white border-t border-slate-800 mt-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/logo-icon.png') }}" alt="RanggaMotret.id" class="w-10 h-10 object-contain shrink-0">
                        <span class="font-extrabold text-lg text-white">RANGGAMOTRET</span>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Platform terintegrasi penyedia layanan dokumentasi fotografi profesional dan akademi edukasi fotografi berbasis web.
                    </p>
                    <div class="flex gap-3 text-slate-400 text-base">
                        <a href="#" class="w-8 h-8 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center hover:text-white transition-colors"><i class="ph-duotone ph-instagram-logo"></i></a>
                        <a href="#" class="w-8 h-8 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center hover:text-white transition-colors"><i class="ph-duotone ph-youtube-logo"></i></a>
                        <a href="#" class="w-8 h-8 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center hover:text-white transition-colors"><i class="ph-duotone ph-whatsapp-logo"></i></a>
                    </div>
                </div>

                <div class="space-y-3 text-xs">
                    <h4 class="font-bold text-white uppercase tracking-wider text-[11px]">Layanan Fotografi</h4>
                    <ul class="space-y-2 text-slate-400">
                        <li><a href="{{ route('public.services') }}" class="hover:text-white">Wedding Documentation</a></li>
                        <li><a href="{{ route('public.services') }}" class="hover:text-white">Prewedding Outdoor</a></li>
                        <li><a href="{{ route('public.services') }}" class="hover:text-white">Graduation & Studio Portrait</a></li>
                        <li><a href="{{ route('public.services') }}" class="hover:text-white">Commercial Product</a></li>
                    </ul>
                </div>

                <div class="space-y-3 text-xs">
                    <h4 class="font-bold text-white uppercase tracking-wider text-[11px]">Akademi & Kursus</h4>
                    <ul class="space-y-2 text-slate-400">
                        <li><a href="{{ route('public.courses') }}" class="hover:text-white">Fotografi Dasar & Exposure</a></li>
                        <li><a href="{{ route('public.courses') }}" class="hover:text-white">Studio Lighting Masterclass</a></li>
                        <li><a href="{{ route('public.courses') }}" class="hover:text-white">Editing Lightroom & Retouching</a></li>
                        <li><a href="{{ route('public.verify-certificate') }}" class="hover:text-white">Verifikasi Sertifikat</a></li>
                    </ul>
                </div>

                <div class="space-y-3 text-xs text-slate-400">
                    <h4 class="font-bold text-white uppercase tracking-wider text-[11px]">Kontak Studio</h4>
                    <p class="flex items-center gap-2"><i class="ph-duotone ph-map-pin text-slate-300"></i> Jl. Studio Fotografi No. 1, Jakarta</p>
                    <p class="flex items-center gap-2"><i class="ph-duotone ph-phone text-slate-300"></i> +62 812-3456-7890</p>
                    <p class="flex items-center gap-2"><i class="ph-duotone ph-envelope text-slate-300"></i> info@ranggamotret.id</p>
                </div>
            </div>

            <div class="border-t border-slate-800 mt-12 pt-6 flex flex-col sm:flex-row justify-between items-center text-[11px] text-slate-400 gap-4">
                <div>&copy; {{ date('Y') }} RanggaMotret.id. Hak Cipta Dilindungi Undang-Undang.</div>
                <div class="flex gap-6">
                    <a href="{{ route('public.consultation') }}" class="hover:text-white">Konsultasi</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp Speed Dial Button -->
    <aside aria-label="Bantuan WhatsApp" class="fixed bottom-6 right-6 z-40">
        <a href="https://wa.me/628123456789?text=Halo%20Studio%20RanggaMotret,%20saya%20ingin%20bertanya%20seputar%20layanan%20foto" 
           target="_blank" 
           rel="noopener noreferrer"
           class="flex items-center gap-2.5 px-4 py-3 rounded-full bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs shadow-2xl shadow-emerald-900/40 hover:scale-105 active:scale-95 transition-all group"
           title="Chat Tim RanggaMotret di WhatsApp">
            <i class="ph-duotone ph-whatsapp-logo text-xl"></i>
            <span class="hidden sm:inline font-bold tracking-wide">Tanya Studio</span>
        </a>
    </aside>

    @stack('scripts')
</body>
</html>
