<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Portal — RanggaMotret.id')</title>

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
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
        }
        .z-100 {
            z-index: 100 !important;
        }
        .modal-backdrop-fixed {
            position: fixed !important;
            inset: 0 !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            z-index: 9999 !important;
        }
        .clean-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
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
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            font-size: 0.75rem;
            font-weight: 700;
            transition: all 0.15s ease;
        }
        .sidebar-link.active {
            background-color: #0f172a;
            color: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.15);
        }
        .sidebar-link:not(.active) {
            color: #475569;
        }
        .sidebar-link:not(.active):hover {
            background-color: #f1f5f9;
            color: #0f172a;
        }

        /* Luxury Slim Smooth Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }
    </style>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon-square.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}" type="image/png">
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-900 antialiased min-h-screen flex" x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" 
         @click="sidebarOpen = false" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/60 z-30 md:hidden" style="display: none;"></div>

    <!-- Sidebar Layout Navigation -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
           class="w-64 bg-white border-r border-slate-200 flex flex-col shrink-0 fixed md:sticky top-0 h-screen z-40 transition-transform duration-300 ease-in-out">
        <div class="p-6 space-y-6 overflow-y-auto grow">
            <!-- Brand Logo -->
            <div class="flex items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('images/logo-icon.png') }}" alt="RanggaMotret.id" class="w-10 h-10 object-contain shrink-0 group-hover:scale-105 transition-transform duration-200">
                    <div class="flex flex-col">
                        <span class="font-extrabold text-base tracking-tight text-slate-900">RANGGA<span class="text-slate-500">MOTRET</span></span>
                        <span class="text-[9px] text-slate-400 font-extrabold uppercase tracking-widest -mt-1">
                            @if(Auth::user()->isOwner())
                                Owner Portal
                            @else
                                Portal Pelanggan
                            @endif
                        </span>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-700">
                    <i class="ph-bold ph-x text-lg"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="space-y-6">
                @if(Auth::user()->isOwner())
                    <!-- 1. OWNER NAVIGATION GROUP -->
                    <div class="space-y-1">
                         <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="ph-duotone ph-chart-line-up text-base w-5"></i>
                            <span>Dashboard</span>
                        </a>

                        <a href="{{ route('admin.payments.index') }}" class="sidebar-link {{ request()->routeIs('admin.payments*') ? 'active' : '' }}">
                            <i class="ph-duotone ph-receipt text-base w-5"></i>
                            <span>Kelola Pembayaran</span>
                        </a>

                        <a href="{{ route('admin.layanan.index') }}" class="sidebar-link {{ request()->routeIs('admin.layanan.*') && !request()->routeIs('admin.kategori-layanan.*') ? 'active' : '' }}">
                            <i class="ph-duotone ph-camera text-base w-5"></i>
                            <span>Kelola Katalog Fotografi</span>
                        </a>

                        <a href="{{ route('admin.kategori-layanan.index') }}" class="sidebar-link {{ request()->routeIs('admin.kategori-layanan.*') ? 'active' : '' }}">
                            <i class="ph-duotone ph-squares-four text-base w-5"></i>
                            <span>Kategori Layanan</span>
                        </a>

                        <a href="{{ route('admin.consultations.index') }}" class="sidebar-link {{ request()->routeIs('admin.consultations*') ? 'active' : '' }}">
                            <i class="ph-duotone ph-chats-circle text-base w-5"></i>
                            <span>Kelola Konsultasi</span>
                        </a>

                        <a href="{{ route('admin.portofolio.index') }}" class="sidebar-link {{ request()->routeIs('admin.portofolio.*') ? 'active' : '' }}">
                            <i class="ph-duotone ph-images text-base w-5"></i>
                            <span>Kelola Portofolio</span>
                        </a>

                        <a href="{{ route('admin.courses.index') }}" class="sidebar-link {{ request()->routeIs('admin.courses*') && !request()->routeIs('admin.courses.reservations.*') ? 'active' : '' }}">
                            <i class="ph-duotone ph-graduation-cap text-base w-5"></i>
                            <span>Kelola Kursus & Materi Kursus</span>
                        </a>

                        <a href="{{ route('admin.bookings.index') }}" class="sidebar-link {{ request()->routeIs('admin.bookings*') ? 'active' : '' }}">
                            <i class="ph-duotone ph-calendar-check text-base w-5"></i>
                            <span>Kelola Reservasi Fotografi</span>
                        </a>

                        <a href="{{ route('admin.courses.reservations.index') }}" class="sidebar-link {{ request()->routeIs('admin.courses.reservations.*') ? 'active' : '' }}">
                            <i class="ph-duotone ph-receipt text-base w-5"></i>
                            <span>Kelola Reservasi Kursus</span>
                        </a>



                        <a href="{{ route('calendar') }}" class="sidebar-link {{ request()->routeIs('calendar') ? 'active' : '' }}">
                            <i class="ph-duotone ph-calendar-blank text-base w-5"></i>
                            <span>Kelola Jadwal</span>
                        </a>

                        <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                            <i class="ph-duotone ph-users text-base w-5"></i>
                            <span>Kelola Pengguna</span>
                        </a>

                        <!-- Menu Laporan & Submenu -->
                        <div x-data="{ openReports: {{ request()->routeIs('admin.reports*') || request()->routeIs('laporan.*') ? 'true' : 'false' }} }" class="space-y-1">
                            <button type="button" 
                                    @click="openReports = !openReports" 
                                    class="sidebar-link w-full flex items-center justify-between {{ request()->routeIs('admin.reports*') || request()->routeIs('laporan.*') ? 'active' : '' }}">
                                <div class="flex items-center gap-3">
                                    <i class="ph-duotone ph-chart-pie-slice text-base w-5"></i>
                                    <span>Laporan</span>
                                </div>
                                <i class="ph-bold ph-caret-down text-xs transition-transform duration-200" :class="openReports ? 'rotate-180' : ''"></i>
                            </button>

                            <div x-show="openReports" 
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="pl-7 space-y-1 pt-0.5">
                                <a href="{{ route('admin.reports.pendapatan') }}" 
                                   class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-bold transition-colors {{ request()->routeIs('admin.reports.pendapatan') ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                    <i class="ph-duotone ph-wallet text-sm {{ request()->routeIs('admin.reports.pendapatan') ? 'text-emerald-400' : 'text-slate-500' }}"></i>
                                    <span>Laporan Pendapatan</span>
                                </a>

                                <a href="{{ route('admin.reports.reservasi') }}" 
                                   class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-bold transition-colors {{ request()->routeIs('admin.reports.reservasi') ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                    <i class="ph-duotone ph-calendar-check text-sm {{ request()->routeIs('admin.reports.reservasi') ? 'text-indigo-400' : 'text-slate-500' }}"></i>
                                    <span>Laporan Reservasi</span>
                                </a>
                            </div>
                        </div>
                    </div>

                @else
                    <!-- 2. PELANGGAN NAVIGATION GROUP -->
                    <div class="space-y-1">
                        <div class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-2">Utama</div>

                        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') || request()->routeIs('customer.dashboard') ? 'active' : '' }}">
                            <i class="ph-duotone ph-gauge text-base w-5"></i>
                            <span>Dashboard</span>
                        </a>

                        <!-- 1. Menu Layanan Fotografi (Terpadu: Layanan Dipesan, Jadwal & Riwayat Reservasi) -->
                        <a href="{{ route('customer.bookings.index') }}" class="sidebar-link {{ request()->routeIs('customer.bookings.*') || request()->routeIs('reservasi.*') || request()->routeIs('customer.fotografi.*') || request()->routeIs('fotografi.saya') ? 'active' : '' }}">
                            <i class="ph-duotone ph-camera text-base w-5"></i>
                            <span>Layanan Fotografi</span>
                        </a>

                        <!-- 2. Menu Kursus (Terpadu: Kelas Belajar, Progres Materi & Riwayat Pendaftaran) -->
                        <a href="{{ route('student.dashboard') }}" class="sidebar-link {{ request()->routeIs('student.dashboard') || request()->routeIs('student.courses') || request()->routeIs('customer.courses.index') || request()->routeIs('customer.courses.reservations') || request()->routeIs('reservasi.kursus.*') ? 'active' : '' }}">
                            <i class="ph-duotone ph-graduation-cap text-base w-5"></i>
                            <span>Layanan Kursus</span>
                        </a>

                        <!-- 5. Menu Riwayat Konsultasi -->
                        <a href="{{ route('customer.consultations.index') }}" class="sidebar-link {{ request()->routeIs('customer.consultations.*') || request()->routeIs('konsultasi.riwayat') ? 'active' : '' }}">
                            <i class="ph-duotone ph-chats-circle text-base w-5"></i>
                            <span>Riwayat Konsultasi</span>
                        </a>

                        <!-- 6. Menu Riwayat Pembayaran -->
                        <a href="{{ route('payments.index') }}" class="sidebar-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                            <i class="ph-duotone ph-wallet text-base w-5"></i>
                            <span>Riwayat Pembayaran</span>
                        </a>
                    </div>
                @endif
            </nav>
        </div>
    </aside>

    <!-- Main Dashboard Content Shell -->
    <div class="flex-1 flex flex-col min-w-0 min-h-screen">
        <!-- Top Navigation Bar -->
        <header class="bg-white border-b border-slate-200 sticky top-0 z-20 px-4 sm:px-6 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = true" class="md:hidden p-2 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200">
                    <i class="ph-duotone ph-list text-base"></i>
                </button>
            </div>

            <div class="flex items-center gap-3 text-xs font-bold">
                <a href="{{ route('home') }}" class="relative p-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors" title="Lihat Website">
                    <i class="ph-duotone ph-globe text-base"></i>
                </a>

                <!-- Notification Dropdown Preview -->
                <div x-data="{ notifOpen: false }" class="relative">
                    <button @click="notifOpen = !notifOpen" 
                            type="button"
                            class="relative p-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors focus:outline-none focus:ring-2 focus:ring-slate-400" 
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
                            $previewNotifs = Auth::user()->notifikasis()->latest()->take(5)->get();
                        @endphp

                        <div class="max-h-85 overflow-y-auto divide-y divide-slate-100 no-scrollbar">
                            @forelse($previewNotifs as $n)
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

                <!-- Profile Dropdown Menu with Logout -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" 
                            class="flex items-center gap-2.5 p-1 sm:pl-2 sm:pr-3 sm:py-1 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-200 text-left transition-all group focus:outline-none focus:ring-2 focus:ring-slate-400">
                        @if(Auth::user()->avatar)
                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-lg object-cover ring-1 ring-slate-300">
                        @else
                            <div class="w-8 h-8 rounded-lg bg-slate-900 text-white font-bold flex items-center justify-center text-xs shadow-sm">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="hidden sm:block text-left leading-tight">
                            <div class="text-xs font-bold text-slate-900 truncate max-w-30">{{ Auth::user()->name }}</div>
                            <div class="text-[10px] text-slate-500 font-semibold capitalize">{{ Auth::user()->role }}</div>
                        </div>
                        <i class="ph-bold ph-caret-down text-[10px] text-slate-400 group-hover:text-slate-700 transition-transform duration-200 ml-0.5" :class="{ 'rotate-180': open }"></i>
                    </button>

                    <!-- Dropdown Content -->
                    <div x-show="open" 
                         @click.outside="open = false" 
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                         class="absolute right-0 mt-2 w-56 bg-white border border-slate-200 rounded-2xl shadow-xl py-2 z-50 text-xs font-medium"
                         style="display: none;">
                        
                        <!-- User Header Info -->
                        <div class="px-4 py-2.5 border-b border-slate-100">
                            <p class="font-extrabold text-slate-900 truncate">{{ Auth::user()->name }}</p>
                            <p class="text-slate-500 text-[11px] truncate">{{ Auth::user()->email }}</p>
                            <span class="inline-block mt-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[9px] font-bold uppercase tracking-wider">
                                {{ Auth::user()->role }}
                            </span>
                        </div>

                        <!-- Menu Items -->
                        <div class="py-1">
                            <a href="{{ route('profile') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:text-slate-900 hover:bg-slate-50 transition-colors">
                                <i class="ph-duotone ph-user-circle text-base text-slate-500"></i>
                                <span>Profil Saya</span>
                            </a>
                            @if(!Auth::user()->isOwner())
                                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:text-slate-900 hover:bg-slate-50 transition-colors">
                                    <i class="ph-duotone ph-gauge text-base text-slate-500"></i>
                                    <span>Dashboard</span>
                                </a>
                            @endif
                        </div>

                        <div class="border-t border-slate-100 my-1"></div>

                        <!-- Logout Action -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-rose-600 hover:bg-rose-50 font-bold flex items-center gap-2.5 transition-colors">
                                <i class="ph-duotone ph-sign-out text-base"></i>
                                <span>Keluar</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Body Output -->
        <main class="p-4 sm:p-6 md:p-8 grow">
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
             class="fixed top-20 right-4 sm:right-6 z-50 max-w-sm w-full shadow-2xl pointer-events-auto"
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

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
