<?php

namespace App\Http\Controllers;

use App\Events\NewNotificationEvent;
use App\Models\Product;
use Illuminate\Http\Request;

class NotificationController extends Controller
{

    public function index()
    {
        return view('notifications.index');
    }


    public function show($id)
    {
        return response()->json(['message' => 'Notification sent']);
    }

    public function markAsRead(Request $request)
    {
        auth()->user()->unreadNotifications
            ->where('data.type', $request->type)
            ->markAsRead();
        return response()->json(['message' => 'Notifications marked as read']);
    }
}
