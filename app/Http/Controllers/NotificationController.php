<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class NotificationController extends Controller
{
    public function index()
    {
        return view('notifications.index');
    }

    public function getTableUnreadNotifications()
    {
        $notifications = auth()->user()->unreadNotifications;
        return DataTables::of($notifications)
            ->addColumn('id', function ($notification) {
                return $notification->id;
            })
            ->addColumn('name', function ($notification) {
                return $notification->data['name'];
            })
            ->addColumn('type', function ($notification) {
                return $notification->data['type'];
            })
            ->addColumn('formatted_created_at', function ($notification) {
                return $notification->created_at->format('D, d-m-y, G:i');
            })
            ->addColumn('formatted_updated_at', function ($notification) {
                return $notification->updated_at->format('D, d-m-y, G:i');
            })
            ->addColumn('action', 'partials.button-table.unread-notif-action')
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
    }

    public function getTableReadNotifications()
    {
        $notifications = auth()->user()->readNotifications;
        return DataTables::of($notifications)
            ->addColumn('id', function ($notification) {
                return $notification->id;
            })
            ->addColumn('name', function ($notification) {
                return $notification->data['name'];
            })
            ->addColumn('type', function ($notification) {
                return $notification->data['type'];
            })
            ->addColumn('formatted_created_at', function ($notification) {
                return $notification->created_at->format('D, d-m-y, G:i');
            })
            ->addColumn('formatted_updated_at', function ($notification) {
                return $notification->updated_at->format('D, d-m-y, G:i');
            })
            ->addIndexColumn()
            ->make(true);
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
