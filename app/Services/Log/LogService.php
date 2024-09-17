<?php

namespace App\Services\Log;

use App\Models\Log;
use Exception;

class LogService
{

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);
            $user_id = $request->user_id;
            $date = $request->date;

            $logs = Log::with(['user']);

            if (isset($user_id)) {
                $logs->where('user_id', $user_id);
            }

            if (isset($date)) {
                $logs->whereDate('created_at', $date);
            }

            $logs = $logs->paginate($perPage);

            return $logs;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
}
