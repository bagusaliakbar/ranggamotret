@extends('layouts.admin')

@section('title', 'Galeri Proofing Foto — RanggaMotret.id')

@section('content')
<div class="space-y-8" x-data="proofingApp()">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-200 pb-6">
        <div>
            <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200">
                Sesi Pemotretan {{ $booking->nomor_booking }}
            </span>
            <h1 class="text-3xl font-extrabold text-slate-900 mt-2">Proofing & Pemilihan Foto Deliverables</h1>
            <p class="text-xs text-slate-600 font-medium mt-1">Klik tanda bintang pada foto pilihan Anda untuk dicetak atau di-retouch lebih lanjut.</p>
        </div>

        <div class="clean-card px-5 py-3 rounded-2xl border border-slate-200 flex items-center gap-4 text-xs font-bold shadow-sm">
            <div class="text-slate-600">Total Foto: <span class="text-slate-900 font-extrabold">{{ $booking->galeriFotos->count() }}</span></div>
            <div class="h-4 w-px bg-slate-200"></div>
            <div class="text-slate-600">Dipilih: <span class="text-amber-600 font-extrabold" x-text="selectedCount">{{ $booking->galeriFotos->where('is_selected', true)->count() }}</span></div>
        </div>
    </div>

    @if($booking->galeriFotos->isEmpty())
        <div class="clean-card p-12 text-center rounded-3xl space-y-4 shadow-sm">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-700 text-2xl flex items-center justify-center mx-auto border border-slate-200">
                <i class="fa-solid fa-camera animate-pulse"></i>
            </div>
            <h3 class="font-extrabold text-lg text-slate-900">Foto Sedang Dalam Proses Edit</h3>
            <p class="text-xs text-slate-600 max-w-md mx-auto leading-relaxed">Tim fotografer kami sedang menyortir dan mengunggah foto hasil pemotretan Anda. Halaman ini akan diperbarui segera setelah proses upload selesai.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($booking->galeriFotos as $foto)
                <div class="clean-card rounded-2xl overflow-hidden border border-slate-200 group relative flex flex-col justify-between shadow-sm hover:shadow-md transition-all"
                    x-data="{ isSelected: {{ $foto->is_selected ? 'true' : 'false' }} }">
                    
                    <div class="relative h-64 overflow-hidden bg-slate-100">
                        <img src="{{ asset($foto->foto_path) }}" alt="Proofing Photo" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        
                        <!-- Toggle Star Button -->
                        <button @click="toggleSelect({{ $foto->id }}, $data)" class="absolute top-3 right-3 w-10 h-10 rounded-full flex items-center justify-center text-base transition-transform active:scale-90 shadow-md border"
                            :class="isSelected ? 'bg-amber-400 text-slate-900 border-amber-500' : 'bg-white/90 text-slate-400 hover:text-amber-500 border-slate-200'">
                            <i class="fa-solid fa-star"></i>
                        </button>
                    </div>

                    <div class="p-4 flex items-center justify-between text-xs border-t border-slate-100 bg-white">
                        <span class="text-[11px] font-mono text-slate-500 font-bold">#IMG-{{ $foto->id }}</span>
                        <a href="{{ asset($foto->foto_path) }}" download class="text-slate-900 hover:text-slate-700 font-bold flex items-center gap-1.5 transition-colors">
                            <i class="fa-solid fa-download"></i> Unduh
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<script>
    function proofingApp() {
        return {
            selectedCount: {{ $booking->galeriFotos->where('is_selected', true)->count() }},
            toggleSelect(fotoId, component) {
                fetch(`/customer/proofing/${fotoId}/toggle`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        component.isSelected = data.is_selected;
                        if (data.is_selected) {
                            this.selectedCount++;
                        } else {
                            this.selectedCount--;
                        }
                    }
                });
            }
        }
    }
</script>
@endsection
