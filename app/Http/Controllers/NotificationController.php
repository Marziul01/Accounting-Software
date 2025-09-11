<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Notification;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function unread()
    {
        $notifications = Notification::where('Read', 0)
            ->orderBy('sent_date', 'desc')
            ->take(10)
            ->get();

        $unreadCount = Notification::where('Read', 0)->count();

        return response()->json([
            'notifications' => $notifications,
            'count' => $unreadCount
        ]);
    }

    public function markAsRead()
    {
        Notification::where('Read', 0)->update(['Read' => 1]);
        return response()->json(['status' => 'success']);
    }

    public function show($id)
    {
        $notification = Notification::findOrFail($id);
        $contactName = null;
        if ($notification->contact_id) {
            $contactName = Contact::where('id', $notification->contact_id)->value('name');
        }
        return response()->json([
            'notification' => $notification,
            'timeAgo' => Carbon::parse($notification->sent_date)->diffForHumans(),
            'contactName' => $contactName
        ]);
    }

    public function all()
    {
        $notifications = Notification::orderBy('sent_date', 'desc')->paginate(20);
        return view('admin.notifications.all', compact('notifications'));
    }
}
