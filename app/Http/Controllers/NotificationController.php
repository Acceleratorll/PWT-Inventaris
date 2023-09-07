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


    public function sendNotification()
    {
        event(new NewNotificationEvent());

        return response()->json(['message' => 'Notification sent']);
    }
}
