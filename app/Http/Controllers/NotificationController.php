<?php

namespace App\Http\Controllers;

use App\Services\Log\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    private $notificationService;

    public function __construct(NotificationService $notificationService) {
        $this->notificationService = $notificationService;
    }

    public function search(Request $request){
        $result = $this->notificationService->search($request);

        return $result;
    }

    public function see(Request $request){
        $result = $this->notificationService->see($request);

        if($result['status']) $result['message'] = "Notificações vistas";
        return $this->response($result);
    }

    private function response($result){
        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'] ?? null,
            'data' => $result['data'] ?? null,
            'error' => $result['error'] ?? null
        ], $result['statusCode'] ?? 200);
    }

}
