<?php

namespace App\Services\Notification;

use App\Models\Log;
use App\Models\Notification;
use Exception;
use Illuminate\Support\Facades\Auth;

class NotificationService
{

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);
            $auth = Auth::user();

            $notifications = Notification::with(['user']);

            $notifications->where('user_id', $auth->id)
                ->where('is_seen', false);
            

            $notifications = $notifications->paginate($perPage);

            return $notifications;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function see($request)
    {
        try {
            $ids = $request->ids;
            
            $notifications = Notification::whereIn('id', $ids);

            $notifications->update([
                'is_seen' => true,
                'updated_at' => now(),
            ]);            

            return $notifications;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
}
