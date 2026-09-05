<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $filter = strtolower((string) $request->query('filter', 'all'));
        $allowedFilters = ['all', 'unread', 'learning', 'assessment'];

        if (! in_array($filter, $allowedFilters, true)) {
            $filter = 'all';
        }

        $query = $request->user()->notifications()->latest();

        if ($filter === 'unread') {
            $query->whereNull('read_at');
        } elseif (in_array($filter, ['learning', 'assessment'], true)) {
            $query->where('data->category', $filter);
        }

        $notifications = $query
            ->paginate(20)
            ->withQueryString();

        return view('notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => $request->user()->unreadNotifications()->count(),
            'activeFilter' => $filter,
            'filters' => [
                'all' => 'All',
                'unread' => 'Unread',
                'learning' => 'Learning',
                'assessment' => 'Assessment',
            ],
        ]);
    }

    public function markRead(Request $request, string $notification): RedirectResponse
    {
        $item = $request->user()->notifications()->whereKey($notification)->firstOrFail();
        $item->markAsRead();

        $destination = data_get($item->data, 'url');

        if (is_string($destination) && $destination !== '') {
            return redirect()->to($destination);
        }

        return back();
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('status', 'All notifications marked as read.');
    }
}
