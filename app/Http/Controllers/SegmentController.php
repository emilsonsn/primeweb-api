<?php

namespace App\Http\Controllers;

use App\Services\Segment\SegmentService;
use Illuminate\Http\Request;

class SegmentController extends Controller
{
    private $segmentService;

    public function __construct(SegmentService $segmentService) {
        $this->segmentService = $segmentService;
    }

    public function all() {
        $result = $this->segmentService->all();

        return $this->response($result);
    }

    public function search(Request $request){
        $result = $this->segmentService->search($request);

        return $result;
    }

    public function create(Request $request){
        $result = $this->segmentService->create($request);

        if($result['status']) $result['message'] = "Segmento criado com sucesso";
        return $this->response($result);
    }

    public function update(Request $request, $id){
        $result = $this->segmentService->update($request, $id);

        if($result['status']) $result['message'] = "Segmento atualizado com sucesso";
        return $this->response($result);
    }

    public function delete($id){
        $result = $this->segmentService->delete($id);

        if($result['status']) $result['message'] = "Segmento deletado com sucesso";
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
