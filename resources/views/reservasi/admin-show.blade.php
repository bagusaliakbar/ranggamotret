@extends('layouts.admin')

@section('title', 'Detail Reservasi ' . $booking->nomor_booking . ' - Admin')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-12 space-y-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-200 pb-6">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors">Dashboard</a>
                <span>/</span>
                <a href="{{ route('admin.bookings.index') }}" class="hover:text-slate-700 transition-colors">Kelola Reservasi</a>
                <span>/</span>
                <span class="text-slate-700">{{ $booking->nomor_booking }}</span>
            </div>
            <h1 class="text-3xl font-extrabold text-slate-900 mt-1">{{ $booking->nomor_booking }}</h1>
        </div>
        
        <form method="POST" action="{{ route('admin.bookings.update-status', $booking->id) }}" class="flex items-center gap-3">
            @csrf
            <span class="text-xs text-slate-600 font-medium">Ubah Status:</span>
            <select name="status" class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-xs text-slate-900 font-bold focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-colors shadow-sm">
                @foreach(['Menunggu Konfirmasi', 'Disetujui', 'Menunggu Pembayaran', 'Dibayar', 'Terjadwal', 'Selesai', 'Dibatalkan', 'Ditolak'] as $st)
                    <option value="{{ $st }}" {{ $booking->status === $st ? 'selected' : '' }}>{{ $st }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 rounded-xl text-xs font-bold transition-colors shadow-sm">Simpan</button>
        </form>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
        <div class="clean-card p-6 rounded-3xl space-y-5">
            <h3 class="font-bold text-base text-slate-900 border-b border-slate-100 pb-3">Data Pelanggan & Layanan</h3>
            <div class="space-y-4">
                <div><span class="text-slate-500 block mb-1">Nama Customer:</span> <span class="font-extrabold text-slate-900 text-sm">{{ $booking->customer->name }}</span></div>
                <div><span class="text-slate-500 block mb-1">Kontak WA/Email:</span> <span class="text-slate-700 font-medium">{{ $booking->customer->phone ?? '-' }} ({{ $booking->customer->email }})</span></div>
                <div><span class="text-slate-500 block mb-1">Layanan & Paket:</span> <span class="text-slate-900 font-bold">{{ $booking->layanan->nama }} - {{ $booking->paket->nama_paket }}</span></div>
                <div><span class="text-slate-500 block mb-1">Harga Tagihan:</span> <span class="font-extrabold text-slate-900 text-sm">Rp {{ number_format($booking->paket->harga, 0, ',', '.') }}</span></div>
            </div>
        </div>

        <div class="clean-card p-6 rounded-3xl space-y-5">
            <h3 class="font-bold text-base text-slate-900 border-b border-slate-100 pb-3">Jadwal & Lokasi</h3>
            <div class="space-y-4">
                <div><span class="text-slate-500 block mb-1">Tanggal Pemotretan:</span> <span class="font-extrabold text-slate-900 text-sm">{{ date('d F Y', strtotime($booking->tanggal)) }} ({{ $booking->waktu }} WIB)</span></div>
                <div><span class="text-slate-500 block mb-1">Alamat / Venue:</span> <span class="text-slate-700 font-medium">{{ $booking->lokasi }}</span></div>
                <div><span class="text-slate-500 block mb-1">Kebutuhan Khusus:</span> <span class="text-slate-600 leading-relaxed">{{ $booking->kebutuhan_khusus ?? '-' }}</span></div>
            </div>
        </div>
    </div>

    <!-- Admin Photo Deliverables Uploader & Proofing Manager -->
    <div class="clean-card p-8 rounded-3xl space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-100 pb-4">
            <div>
                <h3 class="text-lg font-extrabold text-slate-900">Unggah Hasil Foto Pemotretan (Proofing Deliverables)</h3>
                <p class="text-xs text-slate-500 font-medium mt-1">Foto yang diunggah akan otomatis muncul di halaman proofing galeri customer.</p>
            </div>
            <span class="text-[10px] text-slate-600 font-extrabold uppercase tracking-wider bg-slate-100 px-3 py-1.5 rounded-lg">{{ $booking->galeriFotos->count() }} Foto Terunggah</span>
        </div>

        <form method="POST" action="{{ route('admin.proofing.upload', $booking->id) }}" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-center gap-4 text-xs">
            @csrf
            <input type="file" name="fotos[]" multiple required accept="image/*"
                class="w-full sm:flex-1 px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-900 file:text-white file:cursor-pointer cursor-pointer hover:border-slate-300 transition-colors">
            <button type="submit" class="w-full sm:w-auto bg-slate-900 hover:bg-slate-800 text-white px-6 py-3 rounded-xl font-bold text-xs whitespace-nowrap shadow-md transition-all">
                <i class="fa-solid fa-cloud-arrow-up mr-1"></i> Upload Foto
            </button>
        </form>

        <!-- Existing Photos Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-4 pt-4">
            @foreach($booking->galeriFotos as $foto)
                <div class="relative group rounded-2xl overflow-hidden clean-card shadow-sm border border-slate-200 h-40">
                    <img src="{{ asset($foto->foto_path) }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    @if($foto->is_selected)
                        <div class="absolute top-2 left-2 bg-slate-900 text-white px-2 py-1 rounded text-[9px] font-bold shadow-sm">
                            <i class="fa-solid fa-check mr-1"></i> DIPILIH
                        </div>
                    @endif
                    <form method="POST" action="{{ route('admin.proofing.delete', $foto->id) }}" class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        @csrf
                        <button type="submit" class="w-8 h-8 rounded-lg bg-white/90 text-rose-600 hover:bg-rose-50 text-xs font-bold flex items-center justify-center shadow-md backdrop-blur-sm transition-colors" onclick="return confirm('Hapus foto ini?')">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
