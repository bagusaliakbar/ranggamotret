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
}
