@extends('layouts.admin')

@section('title', 'Kelola Jadwal Pemotretan — RanggaMotret.id')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
@endpush

@section('content')
<div class="space-y-8" 
     x-data="{
        openManageModal: false,
        selectedBooking: {
            id: null,
            nomor_booking: '',
            customer_name: '',
            customer_phone: '',
            layanan_nama: '',
            paket_nama: '',
            tanggal: '',
            waktu: '',
            lokasi: '',
            status: '',
            detail_url: ''
        },
        actionUrl: '',

        init() {
            window.addEventListener('open-schedule-modal', (e) => {
                this.openEditSchedule(e.detail);
            });
        },

        openEditSchedule(props) {
            this.selectedBooking = {
                id: props.booking_id,
                nomor_booking: props.nomor_booking,
                customer_name: props.customer_name,
                customer_phone: props.customer_phone,
                layanan_nama: props.layanan_nama,
                paket_nama: props.paket_nama,
                tanggal: props.tanggal,
                waktu: props.waktu,
                lokasi: props.lokasi,
                status: props.status,
                detail_url: props.detail_url
            };
            this.actionUrl = '{{ url('jadwal') }}/' + props.booking_id;
            this.openManageModal = true;
        }
     }">

    <!-- Page Header & Action Buttons -->
    <div class="border-b border-slate-200 pb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700">Kelola Jadwal</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <span>Kelola Jadwal Pemotretan</span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-indigo-50 text-indigo-700 border border-indigo-200">
                    {{ $totalSesi ?? 0 }} Sesi
                </span>
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-1">
                Kalender jadwal sesi pemotretan interaktif. Klik sesi foto untuk mengatur ulang tanggal, jam, atau status reservasi.
            </p>
        </div>

        <div class="flex items-center gap-3">
            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.bookings.index') }}" 
                   class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all flex items-center gap-2 shadow-2xs">
                    <i class="ph-duotone ph-calendar-check text-base text-slate-600"></i>
                    <span>Daftar Reservasi</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2">
                <i class="ph-duotone ph-check-circle text-lg text-emerald-600"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">
                <i class="ph ph-x"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2">
                <i class="ph-duotone ph-warning-circle text-lg text-rose-600"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-rose-600 hover:text-rose-900">
                <i class="ph ph-x"></i>
            </button>
        </div>
    @endif

    <!-- Quick Stats Cards (Ringkasan Sesi) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- 1. Total Sesi -->
        <div class="clean-card p-5 rounded-2xl space-y-1 bg-white border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Total Sesi Foto</span>
                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-camera"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ number_format($totalSesi ?? 0) }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Seluruh pemesanan</div>
        </div>

        <!-- 2. Terjadwal & Aktif -->
        <div class="clean-card p-5 rounded-2xl space-y-1 bg-white border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Sesi Terjadwal</span>
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm border border-blue-100">
                    <i class="ph-duotone ph-calendar-check"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-blue-600">{{ number_format($sesiTerjadwal ?? 0) }}</div>
            <div class="text-[11px] text-blue-700 font-medium">Slot terkonfirmasi & lunas</div>
        </div>

        <!-- 3. Menunggu Konfirmasi -->
        <div class="clean-card p-5 rounded-2xl space-y-1 bg-white border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Menunggu Konfirmasi</span>
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm border border-amber-100">
                    <i class="ph-duotone ph-clock animate-pulse"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-amber-600">{{ number_format($sesiPending ?? 0) }}</div>
            <div class="text-[11px] text-amber-700 font-medium">Perlu review jadwal</div>
        </div>

        <!-- 4. Selesai -->
        <div class="clean-card p-5 rounded-2xl space-y-1 bg-white border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Selesai Pemotretan</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm border border-emerald-100">
                    <i class="ph-duotone ph-check-circle"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-emerald-600">{{ number_format($sesiSelesai ?? 0) }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Dokumentasi terlaksana</div>
        </div>
    </div>

    <!-- Petunjuk Interaksi & Keterangan Warna -->
    <div class="flex flex-wrap items-center justify-between gap-3 text-xs bg-slate-50 p-3.5 rounded-2xl border border-slate-200">
        <div class="flex items-center gap-2 text-slate-700 font-medium">
            <i class="ph-duotone ph-info text-base text-slate-900"></i>
            <span><strong>Tips Owner:</strong> Klik salah satu sesi foto di kalender untuk mengatur ulang tanggal, jam, atau status.</span>
        </div>
        <div class="flex items-center gap-4 text-[11px] font-bold">
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span> Terjadwal / Disetujui</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span> Lunas</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Pending</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-slate-600"></span> Selesai</span>
        </div>
    </div>

    <!-- Calendar Container -->
    <div class="clean-card p-6 rounded-3xl shadow-sm bg-white border border-slate-200">
        <div id="calendar" class="text-xs text-slate-900 font-sans"></div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL ATUR ULANG JADWAL (RESCHEDULE MODAL)  -->
    <!-- ========================================== -->
    <div x-show="openManageModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 space-y-6 shadow-2xl border border-slate-200 relative"
             @click.away="openManageModal = false">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-slate-900 text-white flex items-center justify-center text-lg shadow-sm">
                        <i class="ph-duotone ph-calendar-blank"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-slate-900">Atur Jadwal Sesi Pemotretan</h3>
                        <p class="text-[11px] text-slate-400 font-medium">Ubah tanggal, jam, atau status jadwal sesi foto</p>
                    </div>
                </div>
                <button @click="openManageModal = false" class="text-slate-400 hover:text-slate-700 p-2 rounded-xl hover:bg-slate-100">
                    <i class="ph-bold ph-x text-base"></i>
                </button>
            </div>

            <!-- Booking Info Overview -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2 text-xs">
                <div class="flex items-center justify-between">
                    <span class="font-mono font-bold text-slate-900 bg-white px-2 py-0.5 rounded-lg border border-slate-200" x-text="selectedBooking.nomor_booking"></span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-900 text-white" x-text="selectedBooking.status"></span>
                </div>
                <div class="pt-1">
                    <div class="font-extrabold text-slate-900 text-sm" x-text="selectedBooking.customer_name"></div>
                    <div class="text-[11px] text-slate-500 font-medium" x-text="`${selectedBooking.layanan_nama} • Paket: ${selectedBooking.paket_nama}`"></div>
                    <div class="text-[11px] text-slate-500 font-medium mt-0.5 flex items-center gap-1">
                        <i class="ph-duotone ph-map-pin text-slate-400"></i>
                        <span x-text="selectedBooking.lokasi"></span>
                    </div>
                </div>
            </div>

            <!-- Manage Schedule Form -->
            <form method="POST" :action="actionUrl" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- Atur Tanggal & Jam Baru -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Tanggal Pemotretan <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="tanggal" x-model="selectedBooking.tanggal" required
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 font-medium bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Jam Sesi <span class="text-rose-500">*</span>
                        </label>
                        <select name="waktu" x-model="selectedBooking.waktu" required
                                class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 font-medium bg-white">
                            <option value="08:00">08:00 WIB (Pagi)</option>
                            <option value="09:00">09:00 WIB (Pagi)</option>
                            <option value="10:00">10:00 WIB (Pagi)</option>
                            <option value="11:00">11:00 WIB (Siang)</option>
                            <option value="13:00">13:00 WIB (Siang)</option>
                            <option value="14:00">14:00 WIB (Siang)</option>
                            <option value="15:00">15:00 WIB (Sore)</option>
                            <option value="16:00">16:00 WIB (Sore)</option>
                            <option value="17:00">17:00 WIB (Golden Hour)</option>
                            <option value="19:00">19:00 WIB (Malam)</option>
                        </select>
                    </div>
                </div>

                <!-- Update Status Sesi -->
                @if(Auth::user()->isAdmin())
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Status Jadwal & Reservasi</label>
                        <select name="status" x-model="selectedBooking.status"
                                class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 font-medium bg-white">
                            <option value="Menunggu Konfirmasi">Menunggu Konfirmasi</option>
                            <option value="Disetujui">Disetujui</option>
                            <option value="Terjadwal">Terjadwal</option>
                            <option value="Dibayar">Dibayar (Lunas)</option>
                            <option value="Selesai">Selesai Pemotretan</option>
                            <option value="Dibatalkan">Dibatalkan</option>
                            <option value="Ditolak">Ditolak</option>
                        </select>
                    </div>
                @endif

                <p class="text-[10px] text-slate-400 leading-relaxed italic">
                    * Sistem akan memverifikasi otomatis ketersediaan slot agar tidak terjadi bentrok jadwal dengan pemotretan lain. Notifikasi perubahan jadwal akan otomatis terkirim ke pelanggan.
                </p>

                <!-- Action Submit -->
                <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                    <a :href="selectedBooking.detail_url" class="text-xs font-bold text-indigo-600 hover:underline flex items-center gap-1">
                        <i class="ph-duotone ph-arrow-square-out text-sm"></i>
                        <span>Buka Detail Reservasi</span>
                    </a>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="openManageModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-bold shadow-md hover:shadow-lg transition-all flex items-center gap-1.5">
                            <i class="ph-duotone ph-check-circle text-emerald-400 text-sm"></i>
                            <span>Simpan Jadwal Baru</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var eventsData = @json($events);

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listMonth'
            },
            locale: 'id',
            events: eventsData,
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            },
            height: 'auto',
            dayMaxEvents: true,
            eventDidMount: function(info) {
                info.el.style.cursor = 'pointer';
                info.el.setAttribute('title', 'Klik untuk mengatur jadwal sesi pemotretan ini');
            },
            eventClick: function(info) {
                info.jsEvent.preventDefault();
                if (info.event && info.event.extendedProps) {
                    window.dispatchEvent(new CustomEvent('open-schedule-modal', {
                        detail: info.event.extendedProps
                    }));
                }
            }
        });
        calendar.render();
    });
</script>
@endpush
