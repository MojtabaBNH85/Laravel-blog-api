<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponseTrait;
    public function index(Request $request)
    {
        $user = $request->user();

        return $this->successResponse([
            'notifications' => $user->notifications,
            'unread_Count' => $user->unreadNotifications()->count(),
        ] , 'Notifications received successfully.' , 200);
    }

    public function markAsRead(Request $request , $id)
    {
        $notification = $request->user()->notifications()->find($id);

        if (!$notification) {
            return $this->errorResponse('Notification not found', 404);
        }

        $notification->markAsRead();

        return $this->successResponse(massage: "Notifications Mark as Read");
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return $this->successResponse(massage: "All notifications marked as read" , status: 200);
    }

    public function destroy(Request $request , $id){
        $notification = $request->user()->notifications()->find($id);
        if (!$notification) {
            return $this->errorResponse('Notification not found', 404);
        }

        $notification->delete();

        return $this->successResponse(massage: "Notifications Deleted Successfully" , status: 200);
    }
}
