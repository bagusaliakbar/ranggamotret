@extends('layouts.admin')

@section('title', 'Kelola Pengguna — RanggaMotret.id')

@section('content')
<div class="space-y-8" x-data="userManagementApp()">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700">Kelola Pengguna</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Kelola Pengguna</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Kelola data akun Owner (Administrator) dan Pelanggan (Customer / Peserta Kursus).</p>
        </div>

        <button @click="openCreateModal()" class="btn-dark px-4 py-2.5 rounded-xl text-xs font-bold shadow-md flex items-center gap-2">
            <i class="ph-duotone ph-user-plus"></i> Tambah Pengguna Baru
        </button>
    </div>

    <!-- 3 Stats Cards Grid (Owner & Pelanggan) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
        <!-- 1. Total Pengguna -->
        <div class="clean-card p-5 rounded-2xl space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Total Pengguna</span>
                <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-users"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ $totalUsers ?? 0 }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Semua akun terdaftar</div>
        </div>        <!-- 2. Owner -->
        <div class="clean-card p-5 rounded-2xl space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Owner</span>
                <div class="w-8 h-8 rounded-lg bg-slate-900 text-white flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-shield-check"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ $totalOwners ?? ($totalAdmins ?? 0) }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Pengelola studio & kursus</div>
        </div>

        <!-- 3. Pelanggan -->
        <div class="clean-card p-5 rounded-2xl space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Pelanggan</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-user-circle-check"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-emerald-700">{{ $totalPelanggan ?? ($totalCustomers ?? 0) }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Klien fotografi & akademi</div>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="clean-card p-4 sm:p-5 rounded-2xl">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 text-xs">
            <div class="sm:col-span-5 relative">
                <i class="ph-duotone ph-magnifying-glass absolute left-3.5 top-3.5 text-slate-400"></i>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama, email, atau no. telp..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900 font-medium">
            </div>

            <div class="sm:col-span-3">
                <select name="role" class="w-full px-3 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900 font-medium">
                    <option value="">Semua Hak Akses (Role)</option>
                    <option value="owner" {{ in_array($role ?? '', ['owner', 'admin']) ? 'selected' : '' }}>Owner</option>
                    <option value="pelanggan" {{ in_array($role ?? '', ['pelanggan', 'customer', 'peserta']) ? 'selected' : '' }}>Pelanggan</option>
                </select>
            </div>v>

            <div class="sm:col-span-2">
                <select name="status" class="w-full px-3 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900 font-medium">
                    <option value="">Semua Status</option>
                    <option value="active" {{ ($status ?? '') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ ($status ?? '') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="w-full btn-dark py-2.5 rounded-xl font-bold text-center">
                    Filter
                </button>
                @if($search || $role || $status)
                    <a href="{{ route('admin.users.index') }}" class="p-2.5 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 text-center flex items-center justify-center shrink-0" title="Reset Filter">
                        <i class="ph-duotone ph-arrows-counter-clockwise text-base"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Users Table Card -->
    <div class="clean-card rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-100 text-slate-900 uppercase font-extrabold text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Pengguna</th>
                        <th class="px-6 py-4">Kontak / No. Telepon</th>
                        <th class="px-6 py-4">Role & Hak Akses</th>
                        <th class="px-6 py-4">Status Akun</th>
                        <th class="px-6 py-4">Bergabung</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- User Info -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-slate-900 text-white font-extrabold flex items-center justify-center text-xs shrink-0 shadow-sm">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div class="space-y-0.5">
                                        <div class="font-extrabold text-slate-900 text-xs flex items-center gap-2">
                                            <span>{{ $u->name }}</span>
                                            @if($u->id === Auth::id())
                                                <span class="text-[9px] bg-amber-100 text-amber-800 px-1.5 py-0.5 rounded font-bold">Anda</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-slate-500 font-medium">{{ $u->email }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Contact -->
                            <td class="px-6 py-4 font-medium text-slate-700">
                                @if($u->phone)
                                    <div class="flex items-center gap-1.5">
                                        <i class="fa-solid fa-phone text-slate-400 text-[10px]"></i>
                                        <span>{{ $u->phone }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">-</span>
                                @endif
                            </td>

                            <!-- Role -->
                            <td class="px-6 py-4">
                                @php
                                    $isOwner = in_array($u->role, ['owner', 'admin']);
                                    $roleBadge = $isOwner ? 'bg-slate-900 text-white border-slate-900' : 'bg-emerald-50 text-emerald-800 border-emerald-200';
                                    $roleLabel = $isOwner ? 'Owner' : 'Pelanggan';
                                @endphp
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase border {{ $roleBadge }}">
                                    {{ $roleLabel }}
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4">
                                <form method="POST" action="{{ route('admin.users.toggle-status', $u->id) }}">
                                    @csrf
                                    @if($u->status === 'active')
                                        <button type="submit" 
                                                {{ $u->id === Auth::id() ? 'disabled' : '' }}
                                                class="px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase border bg-emerald-50 text-emerald-800 border-emerald-200 hover:bg-emerald-100 transition-colors flex items-center gap-1.5"
                                                title="Klik untuk menonaktifkan">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Aktif
                                        </button>
                                    @else
                                        <button type="submit" 
                                                {{ $u->id === Auth::id() ? 'disabled' : '' }}
                                                class="px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase border bg-rose-50 text-rose-800 border-rose-200 hover:bg-rose-100 transition-colors flex items-center gap-1.5"
                                                title="Klik untuk mengaktifkan">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span> Nonaktif
                                        </button>
                                    @endif
                                </form>
                            </td>

                            <!-- Joined Date -->
                            <td class="px-6 py-4 text-[11px] text-slate-500 font-medium">
                                {{ $u->created_at->format('d M Y') }}
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Edit Button -->
                                    <button @click="openEditModal({{ json_encode($u) }})" 
                                            class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-colors"
                                            title="Edit Pengguna">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <!-- Delete Button -->
                                    @if($u->id !== Auth::id())
                                        <button @click="openDeleteModal({{ json_encode($u) }})" 
                                                class="p-2 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold transition-colors"
                                                title="Hapus Pengguna">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-xs font-medium">
                                <i class="fa-solid fa-users text-3xl mb-2 block text-slate-300"></i>
                                Tidak ada data pengguna yang sesuai dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- 1. Modal Tambah Pengguna Baru -->
    <div x-show="createModalOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" 
         style="display: none;">
        
        <div @click.away="createModalOpen = false" class="clean-card rounded-3xl p-6 sm:p-8 max-w-lg w-full space-y-6 shadow-2xl bg-white">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-user-plus"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-base text-slate-900">Tambah Pengguna Baru</h3>
                        <p class="text-xs text-slate-500 font-medium">Buat akun untuk Owner (Administrator) atau Pelanggan.</p>
                    </div>
                </div>
                <button @click="createModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block text-slate-700 font-bold mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Budi Santoso"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900 font-medium">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Email <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" required placeholder="nama@email.com"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900 font-medium">
                    </div>
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Password <span class="text-rose-500">*</span></label>
                        <input type="password" name="password" required minlength="8" placeholder="Min. 8 karakter"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900 font-medium">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Role / Hak Akses <span class="text-rose-500">*</span></label>
                        <select name="role" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900 font-medium">
                            <option value="pelanggan">Pelanggan</option>
                            <option value="owner">Owner</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Status Akun <span class="text-rose-500">*</span></label>
                        <select name="status" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900 font-medium">
                            <option value="active">Aktif</option>
                            <option value="inactive">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-slate-700 font-bold mb-1">No. Telepon / WhatsApp</label>
                    <input type="text" name="phone" placeholder="081234567890"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900 font-medium">
                </div>

                <div>
                    <label class="block text-slate-700 font-bold mb-1">Alamat Lengkap</label>
                    <textarea name="address" rows="2" placeholder="Alamat domisili pengguna (opsional)"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900 font-medium"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="createModalOpen = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="btn-dark px-5 py-2.5 rounded-xl font-bold shadow-md">
                        Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 2. Modal Edit Pengguna -->
    <div x-show="editModalOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" 
         style="display: none;">
        
        <div @click.away="editModalOpen = false" class="clean-card rounded-3xl p-6 sm:p-8 max-w-lg w-full space-y-6 shadow-2xl bg-white">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-user-pen"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-base text-slate-900">Edit Data Pengguna</h3>
                        <p class="text-xs text-slate-500 font-medium">Perbarui profil, role, atau ganti password akun.</p>
                    </div>
                </div>
                <button @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form method="POST" :action="`/admin/users/${selectedUser.id}`" class="space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-slate-700 font-bold mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" x-model="selectedUser.name" required
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900 font-medium">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Email <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" x-model="selectedUser.email" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900 font-medium">
                    </div>
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Password Baru <span class="text-[10px] font-normal text-slate-400">(Opsional)</span></label>
                        <input type="password" name="password" minlength="8" placeholder="Kosongkan jika tidak ubah"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900 font-medium">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Role / Hak Akses <span class="text-rose-500">*</span></label>
                        <select name="role" x-model="selectedUser.role" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900 font-medium">
                            <option value="pelanggan">Pelanggan</option>
                            <option value="owner">Owner</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Status Akun <span class="text-rose-500">*</span></label>
                        <select name="status" x-model="selectedUser.status" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900 font-medium">
                            <option value="active">Aktif</option>
                            <option value="inactive">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-slate-700 font-bold mb-1">No. Telepon / WhatsApp</label>
                    <input type="text" name="phone" x-model="selectedUser.phone" placeholder="081234567890"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900 font-medium">
                </div>

                <div>
                    <label class="block text-slate-700 font-bold mb-1">Alamat Lengkap</label>
                    <textarea name="address" rows="2" x-model="selectedUser.address" placeholder="Alamat domisili pengguna"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900 font-medium"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="editModalOpen = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="btn-dark px-5 py-2.5 rounded-xl font-bold shadow-md">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. Modal Konfirmasi Hapus Pengguna -->
    <div x-show="deleteModalOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" 
         style="display: none;">
        
        <div @click.away="deleteModalOpen = false" class="clean-card rounded-3xl p-6 sm:p-8 max-w-md w-full space-y-6 shadow-2xl bg-white text-center">
            <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 text-2xl flex items-center justify-center mx-auto border border-rose-100">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>

            <div class="space-y-2">
                <h3 class="font-extrabold text-lg text-slate-900">Hapus Pengguna Ini?</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Apakah Anda yakin ingin menghapus akun <strong class="text-slate-900" x-text="selectedUser.name"></strong> (<span x-text="selectedUser.email"></span>)? Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>

            <form method="POST" :action="`/admin/users/${selectedUser.id}`" class="flex items-center justify-center gap-3">
                @csrf
                @method('DELETE')
                <button type="button" @click="deleteModalOpen = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md">
                    Ya, Hapus Pengguna
                </button>
            </form>
        </div>
    </div>

</div>

<script>
    function userManagementApp() {
        return {
            createModalOpen: false,
            editModalOpen: false,
            deleteModalOpen: false,
            selectedUser: {
                id: '',
                name: '',
                email: '',
                role: 'pelanggan',
                status: 'active',
                phone: '',
                address: ''
            },
            openCreateModal() {
                this.createModalOpen = true;
            },
            openEditModal(user) {
                const isOwner = ['owner', 'admin'].includes(user.role);
                this.selectedUser = {
                    id: user.id,
                    name: user.name || '',
                    email: user.email || '',
                    role: isOwner ? 'owner' : 'pelanggan',
                    status: user.status || 'active',
                    phone: user.phone || '',
                    address: user.address || ''
                };
                this.editModalOpen = true;
            },
            openDeleteModal(user) {
                this.selectedUser = user;
                this.deleteModalOpen = true;
            }
        }
    }
</script>
@endsection
