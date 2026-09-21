<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function unread()
    {
        if (! auth()->check()) {
            return response()->json(['count' => 0, 'notifications' => []]);
        }

        $unread = auth()->user()->unreadNotifications;
        $notifications = $unread->map(function ($notif) {
            return [
                'id' => $notif->id,
                'read' => false,
                'type' => $notif->data['type'] ?? 'info',
                'item_name' => $notif->data['item_name'] ?? 'Pemberitahuan Baru',
                'message' => $notif->data['message'] ?? '',
                'time' => $notif->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'count' => $unread->count(),
            'notifications' => $notifications,
        ]);
    }

    public function all()
    {
        if (! auth()->check()) {
            return response()->json(['count' => 0, 'notifications' => []]);
        }

        $all = auth()->user()->notifications()->take(50)->get();
        $notifications = $all->map(function ($notif) {
            return [
                'id' => $notif->id,
                'read' => $notif->read_at !== null,
                'type' => $notif->data['type'] ?? 'info',
                'item_name' => $notif->data['item_name'] ?? 'Pemberitahuan',
                'message' => $notif->data['message'] ?? '',
                'time' => $notif->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'count' => $notifications->count(),
            'unread_count' => auth()->user()->unreadNotifications()->count(),
            'notifications' => $notifications,
        ]);
    }

    public function markAsRead(Request $request)
    {
        if ($request->has('id')) {
            $notification = auth()->user()->notifications()->find($request->id);
            if ($notification) {
                $notification->markAsRead();
            }
        } else {
            auth()->user()->unreadNotifications->markAsRead();
        }

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Notifikasi ditandai dibaca.']);
        }

        return redirect()->back()->with('success', 'Notifikasi berhasil ditandai sudah dibaca.');
    }
}
