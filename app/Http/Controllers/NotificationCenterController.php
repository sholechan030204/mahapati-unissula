<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NotificationCenterController extends Controller
{
    public function index(Request $request): View
    {
        return view('notifications.index', [
            'notifications' => $request->user()->notifications()->latest()->paginate(20),
            'unreadCount' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    public function open(Request $request, string $notification): RedirectResponse
    {
        $item = $this->ownedNotification($request, $notification);
        $item->markAsRead();
        $path = parse_url((string) ($item->data['url'] ?? ''), PHP_URL_PATH);
        $query = parse_url((string) ($item->data['url'] ?? ''), PHP_URL_QUERY);

        return redirect(url(is_string($path) && Str::startsWith($path, '/') ? $path : '/').($query ? '?'.$query : ''));
    }

    public function markRead(Request $request, string $notification): RedirectResponse
    {
        $this->ownedNotification($request, $notification)->markAsRead();

        return back()->with('status', 'Notifikasi ditandai sudah dibaca.');
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return back()->with('status', 'Semua notifikasi ditandai sudah dibaca.');
    }

    private function ownedNotification(Request $request, string $notification): DatabaseNotification
    {
        abort_unless(Str::isUuid($notification), 404);

        return $request->user()->notifications()->whereKey($notification)->firstOrFail();
    }
}
