<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationRedirectController extends Controller
{
    /**
     * Mark the notification as read and redirect to its destination.
     *
     * Using a real HTTP redirect guarantees the routing logic runs
     * in a fresh PHP request — immune to opcode-cache staleness — and
     * that the destination URL is resolved at click-time, not render-time.
     */
    public function __invoke(Request $request, Notification $notification)
    {
        // Security: only the owner can open their notifications
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        // Mark read
        if (!$notification->read) {
            $notification->update(['read' => true]);
        }

        $url = $notification->getUrl();

        // Fallback: if still unresolved send to role-appropriate dashboard
        if ($url === '#' || empty($url)) {
            $url = Auth::user()->activeRole() === 'freelancer'
                ? route('freelancer.dashboard')
                : route('client.dashboard');
        }

        return redirect($url);
    }
}
