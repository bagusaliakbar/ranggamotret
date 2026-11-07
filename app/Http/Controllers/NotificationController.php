<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notifikasi;

class NotificationController extends Controller
{
    public function index()
    {
        $notifikasis = Notifikasi::where('user_id', Auth::id())->latest()->get();
        return view('notifications.index', compact('notifikasis'));
    }

    /**
     * Endpoint polling realtime notifikasi
     */
    public function poll(Request $request)
    {
        $user = Auth::user();
        $unreadCount = $user->unreadNotificationsCount();
        $latestNotifs = $user->notifikasis()->latest()->take(5)->get();

        $items = $latestNotifs->map(function ($n) {
            $icon = 'ph-bell-simple';
            $titleLower = strtolower($n->judul);
            if (str_contains($titleLower, 'pembayaran') || str_contains($titleLower, 'transaksi')) {
                $icon = 'ph-receipt';
            } elseif (str_contains($titleLower, 'booking') || str_contains($titleLower, 'reservasi') || str_contains($titleLower, 'jadwal')) {
                $icon = 'ph-calendar-check';
            } elseif (str_contains($titleLower, 'kursus') || str_contains($titleLower, 'materi')) {
                $icon = 'ph-graduation-cap';
            } elseif (str_contains($titleLower, 'konsultasi')) {
                $icon = 'ph-chats-circle';
            }

            return [
                'id' => $n->id,
                'judul' => $n->judul,
                'pesan' => $n->pesan,
                'link' => route('notifications.read', $n->id),
                'is_read' => (bool)$n->is_read,
                'time_ago' => $n->created_at->diffForHumans(),
                'icon' => $icon,
                'created_at' => $n->created_at->toISOString(),
            ];
        });

        $latestId = $latestNotifs->first()?->id ?? 0;

        return response()->json([
            'unread_count' => $unreadCount,
            'latest_id' => $latestId,
            'notifications' => $items,
        ]);
    }

    public function markAsRead($id)
    {
        $notif = Notifikasi::where('user_id', Auth::id())->findOrFail($id);
        $notif->is_read = true;
        $notif->save();

        if ($notif->link) {
            return redirect($notif->link);
        }

        return back();
    }

    public function markAllAsRead()
    {
        Notifikasi::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }
}
