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

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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
           class="w-64 bg-white border-r border-slate-200 flex flex-col justify-between shrink-0 fixed md:sticky top-0 h-screen z-40 transition-transform duration-300 ease-in-out">
        <div class="p-6 space-y-6 overflow-y-auto">
            <!-- Brand Logo -->
            <div class="flex items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('images/logo-icon.png') }}" alt="RanggaMotret.id" class="w-10 h-10 object-contain shrink-0 group-hover:scale-105 transition-transform duration-200">
                    <div class="flex flex-col">
                        <span class="font-extrabold text-base tracking-tight text-slate-900">RANGGA<span class="text-slate-500">MOTRET</span></span>
                        <span class="text-[9px] text-slate-400 font-extrabold uppercase tracking-widest -mt-1">
                            @if(Auth::user()->isAdmin())
                                Admin Portal
                            @elseif(Auth::user()->isInstruktur())
                                Instructor Portal
                            @else
                                Member Portal
                            @endif
                        </span>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-700">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="space-y-6">
                @if(Auth::user()->isAdmin())
                    <!-- 1. ADMIN NAVIGATION GROUP -->
                    <div class="space-y-1">
                        <div class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-2">Utama</div>

                        <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="fa-solid fa-chart-line text-sm w-5"></i>
                            <span>Dashboard</span>
                        </a>

                        <a href="{{ route('admin.bookings.index') }}" class="sidebar-link {{ request()->routeIs('admin.bookings*') ? 'active' : '' }}">
                            <i class="fa-solid fa-calendar-check text-sm w-5"></i>
                            <span>Kelola Booking</span>
                        </a>

                        <a href="{{ route('admin.payments.index') }}" class="sidebar-link {{ request()->routeIs('admin.payments*') ? 'active' : '' }}">
                            <i class="fa-solid fa-receipt text-sm w-5"></i>
                            <span>Verifikasi Pembayaran</span>
                        </a>
                    </div>

                    <div class="space-y-1">
                        <div class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-2">Layanan & Akademi</div>

                        <a href="{{ route('admin.layanan.index') }}" class="sidebar-link {{ request()->routeIs('admin.layanan.*') && !request()->routeIs('admin.kategori-layanan.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-camera-retro text-sm w-5"></i>
                            <span>Kelola Layanan & Paket</span>
                        </a>

                        <a href="{{ route('admin.kategori-layanan.index') }}" class="sidebar-link {{ request()->routeIs('admin.kategori-layanan.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-layer-group text-sm w-5"></i>
                            <span>Kategori Layanan</span>
                        </a>

                        <a href="{{ route('admin.courses.index') }}" class="sidebar-link {{ request()->routeIs('admin.courses*') ? 'active' : '' }}">
                            <i class="fa-solid fa-graduation-cap text-sm w-5"></i>
                            <span>Kursus & Modul LMS</span>
                        </a>

                        <a href="{{ route('admin.consultations.index') }}" class="sidebar-link {{ request()->routeIs('admin.consultations*') ? 'active' : '' }}">
                            <i class="fa-solid fa-comments text-sm w-5"></i>
                            <span>Konsultasi Klien</span>
                        </a>

                        <a href="{{ route('admin.testimonials.index') }}" class="sidebar-link {{ request()->routeIs('admin.testimonials*') ? 'active' : '' }}">
                            <i class="fa-solid fa-star text-sm w-5"></i>
                            <span>Moderasi Testimoni</span>
                        </a>

                        <a href="{{ route('calendar') }}" class="sidebar-link {{ request()->routeIs('calendar') ? 'active' : '' }}">
                            <i class="fa-solid fa-calendar-days text-sm w-5"></i>
                            <span>Kalender Sesi Foto</span>
                        </a>
                    </div>

                    <div class="space-y-1">
                        <div class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-2">Pengguna & Akses</div>

                        <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                            <i class="fa-solid fa-users-gear text-sm w-5"></i>
                            <span>Kelola Pengguna</span>
                        </a>
                    </div>

                    <div class="space-y-1">
                        <div class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-2">Laporan</div>

                        <a href="{{ route('admin.reports.index') }}" class="sidebar-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
                            <i class="fa-solid fa-chart-pie text-sm w-5"></i>
                            <span>Laporan Keuangan</span>
                        </a>
                    </div>

                @elseif(Auth::user()->isInstruktur())
                    <!-- 2. INSTRUCTOR NAVIGATION GROUP -->
                    <div class="space-y-1">
                        <div class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-2">Instruktur Portal</div>

                        <a href="{{ route('instructor.dashboard') }}" class="sidebar-link {{ request()->routeIs('instructor.dashboard') ? 'active' : '' }}">
                            <i class="fa-solid fa-chalkboard-user text-sm w-5"></i>
                            <span>Panel Kursus Saya</span>
                        </a>

                        <a href="{{ route('instructor.assignments.index') }}" class="sidebar-link {{ request()->routeIs('instructor.assignments*') ? 'active' : '' }}">
                            <i class="fa-solid fa-pen-ruler text-sm w-5"></i>
                            <span>Penilaian Tugas Siswa</span>
                        </a>

                        <a href="{{ route('calendar') }}" class="sidebar-link {{ request()->routeIs('calendar') ? 'active' : '' }}">
                            <i class="fa-solid fa-calendar-days text-sm w-5"></i>
                            <span>Jadwal Mengajar</span>
                        </a>
                    </div>

                @else
                    <!-- 3. CUSTOMER / MEMBER NAVIGATION GROUP -->
                    <div class="space-y-1">
                        <div class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-2">Utama</div>

                        <a href="{{ route('customer.dashboard') }}" class="sidebar-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
                            <i class="fa-solid fa-gauge text-sm w-5"></i>
                            <span>Dashboard Saya</span>
                        </a>

                        <a href="{{ route('customer.bookings.index') }}" class="sidebar-link {{ request()->routeIs('customer.bookings.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-calendar-check text-sm w-5"></i>
                            <span>Pesanan & Sesi Foto</span>
                        </a>

                        <a href="{{ route('payments.index') }}" class="sidebar-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-receipt text-sm w-5"></i>
                            <span>Riwayat Pembayaran</span>
                        </a>

                        <a href="{{ route('calendar') }}" class="sidebar-link {{ request()->routeIs('calendar') ? 'active' : '' }}">
                            <i class="fa-solid fa-calendar-days text-sm w-5"></i>
                            <span>Kalender Sesi Saya</span>
                        </a>
                    </div>

                    <div class="space-y-1">
                        <div class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-2">Akademi Fotografi</div>

                        <a href="{{ route('student.dashboard') }}" class="sidebar-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                            <i class="fa-solid fa-graduation-cap text-sm w-5"></i>
                            <span>Ruang Belajar (LMS)</span>
                        </a>
                    </div>

                    <div class="space-y-1">
                        <div class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-2">Eksplor Layanan</div>

                        <a href="{{ route('public.services') }}" class="sidebar-link">
                            <i class="fa-solid fa-camera-retro text-sm w-5"></i>
                            <span>Booking Layanan Baru</span>
                        </a>

                        <a href="{{ route('public.courses') }}" class="sidebar-link">
                            <i class="fa-solid fa-book-open-reader text-sm w-5"></i>
                            <span>Katalog Kursus</span>
                        </a>

                        <a href="{{ route('public.consultation') }}" class="sidebar-link">
                            <i class="fa-solid fa-comments text-sm w-5"></i>
                            <span>Konsultasi Fotografer</span>
                        </a>
                    </div>
                @endif

                <!-- Shared Account Settings Group -->
                <div class="space-y-1">
                    <div class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-2">Akun</div>

                    <a href="{{ route('profile') }}" class="sidebar-link {{ request()->routeIs('profile') ? 'active' : '' }}">
                        <i class="fa-solid fa-user-gear text-sm w-5"></i>
                        <span>Pengaturan Akun</span>
                    </a>
                </div>
            </nav>
        </div>

        <!-- Sidebar Footer Back to Main Web -->
        <div class="p-4 border-t border-slate-200 space-y-3">
            <a href="{{ route('home') }}" class="w-full flex items-center justify-center gap-2 p-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs transition-colors">
                <i class="fa-solid fa-arrow-left text-slate-400"></i>
                <span>Kembali ke Website</span>
            </a>

            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-100 border border-slate-200">
                <div class="flex items-center gap-2.5 truncate">
                    <div class="w-8 h-8 rounded-lg bg-slate-900 text-white font-bold flex items-center justify-center text-xs shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="truncate text-xs">
                        <div class="font-bold text-slate-900 truncate">{{ Auth::user()->name }}</div>
                        <div class="text-[10px] text-slate-500 capitalize">{{ Auth::user()->role }}</div>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Dashboard Content Shell -->
    <div class="flex-1 flex flex-col min-w-0 min-h-screen">
        <!-- Top Navigation Bar -->
        <header class="bg-white border-b border-slate-200 sticky top-0 z-20 px-4 sm:px-6 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = true" class="md:hidden p-2 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200">
                    <i class="fa-solid fa-bars text-sm"></i>
                </button>
                <div class="hidden sm:block">
                    <span class="text-xs font-bold text-slate-400">Selamat datang,</span>
                    <span class="text-xs font-extrabold text-slate-900 ml-1">{{ Auth::user()->name }}</span>
                </div>
            </div>

            <div class="flex items-center gap-3 text-xs font-bold">
                <a href="{{ route('home') }}" class="relative p-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors" title="Lihat Website">
                    <i class="fa-solid fa-globe"></i>
                </a>

                <a href="{{ route('notifications.index') }}" class="relative p-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors" title="Notifikasi">
                    <i class="fa-regular fa-bell"></i>
                    @if(Auth::user()->unreadNotificationsCount() > 0)
                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-slate-900 text-white text-[9px] font-bold rounded-full flex items-center justify-center">
                            {{ Auth::user()->unreadNotificationsCount() }}
                        </span>
                    @endif
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 font-bold transition-colors flex items-center gap-1.5">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span class="hidden sm:inline">Keluar</span>
                    </button>
                </form>
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
                            <i class="fa-solid fa-circle-check text-base"></i>
                        </div>
                        <div class="space-y-0.5">
                            <div class="text-xs font-extrabold text-slate-900">Berhasil</div>
                            <div class="text-xs text-slate-600 font-medium leading-relaxed">{{ session('success') }}</div>
                        </div>
                    </div>
                    <button @click="show = false" class="text-slate-400 hover:text-slate-700 p-1">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xl flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 mt-0.5 border border-rose-100">
                            <i class="fa-solid fa-circle-exclamation text-base"></i>
                        </div>
                        <div class="space-y-0.5">
                            <div class="text-xs font-extrabold text-slate-900">Pemberitahuan</div>
                            <div class="text-xs text-slate-600 font-medium leading-relaxed">{{ session('error') }}</div>
                        </div>
                    </div>
                    <button @click="show = false" class="text-slate-400 hover:text-slate-700 p-1">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
            @endif

            @if(session('status'))
                <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xl flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 mt-0.5 border border-blue-100">
                            <i class="fa-solid fa-circle-info text-base"></i>
                        </div>
                        <div class="space-y-0.5">
                            <div class="text-xs font-extrabold text-slate-900">Informasi</div>
                            <div class="text-xs text-slate-600 font-medium leading-relaxed">{{ session('status') }}</div>
                        </div>
                    </div>
                    <button @click="show = false" class="text-slate-400 hover:text-slate-700 p-1">
                        <i class="fa-solid fa-xmark text-sm"></i>
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
