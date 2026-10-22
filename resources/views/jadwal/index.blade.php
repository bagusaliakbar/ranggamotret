@extends('layouts.admin')

@section('title', 'Kelola Jadwal Pemotretan — RanggaMotret.id')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
@endpush

@section('content')
<div class="space-y-8">
    <div class="border-b border-slate-200 pb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700">Kelola Jadwal</span>
            </div>
            <h1 class="text-3xl font-extrabold text-slate-900">Kelola Jadwal Pemotretan</h1>
            <p class="text-xs text-slate-600 font-medium">Kalender jadwal sesi foto terkonfirmasi dan ketersediaan slot studio.</p>
        </div>
        <a href="{{ route('customer.bookings.create') }}" class="btn-dark px-5 py-3 rounded-xl text-xs font-bold uppercase tracking-wider shadow-md flex items-center gap-1.5">
            <i class="ph-duotone ph-plus"></i> Reservasi Pemotretan
        </a>
    </div>

    <!-- Calendar Container -->
    <div class="clean-card p-6 rounded-2xl shadow-sm bg-white">
        <div id="calendar" class="text-xs text-slate-900"></div>
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
                right: 'dayGridMonth,timeGridWeek'
            },
            events: eventsData,
            eventColor: '#0f172a',
            height: 'auto'
        });
        calendar.render();
    });
</script>
@endpush
