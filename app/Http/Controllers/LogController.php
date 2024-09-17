<?php

namespace App\Http\Controllers;

use App\Services\Log\LogService;
use App\Services\Segment\SegmentService;
use Illuminate\Http\Request;

class LogController extends Controller
{
    private $logService;

    public function __construct(LogService $logService) {
        $this->logService = $logService;
    }

    public function search(Request $request){
        $result = $this->logService->search($request);

        return $result;
    }

}
