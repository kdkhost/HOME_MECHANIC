<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Busca notificações não lidas para o Polling
     */
    public function getUnread()
    {
        $notifications = AdminNotification::where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'count' => AdminNotification::where('is_read', false)->count(),
            'notifications' => $notifications
        ]);
    }

    /**
     * Marca uma notificação como lida
     */
    public function markAsRead($id)
    {
        $notification = AdminNotification::find($id);
        if ($notification) {
            $notification->update(['is_read' => true]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Limpa todas as notificações (marca como lidas)
     */
    public function clearAll()
    {
        AdminNotification::where('is_read', false)->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }
}
