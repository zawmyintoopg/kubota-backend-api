<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification; // Make sure you import the model
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Example: get unread notifications for logged-in user
    public function index()
    {
        $unreadNotifications = Notification::where('user_id', Auth::id())
                                           ->where('read', false)
                                           ->get();

        // You can return JSON (for AJAX) or pass to a view
        return view('notifications.index', compact('unreadNotifications'));
    }
}
