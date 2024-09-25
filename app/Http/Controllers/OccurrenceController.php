<?php

namespace App\Http\Controllers;

use App\Services\Occurence\OccurrenceService;
use Illuminate\Http\Request;

class OccurrenceController extends Controller
{
    private $occurrenceService;

    public function __construct(OccurrenceService $occurrenceService) {
        $this->occurrenceService = $occurrenceService;
    }

    public function all() {
        $result = $this->occurrenceService->all();

        return $this->response($result);
    }

    public function search(Request $request){
        $result = $this->occurrenceService->search($request);

        return $result;
    }

    public function create(Request $request){
        $result = $this->occurrenceService->create($request);

        if($result['status']) $result['message'] = "Ocorrência criada com sucesso";
        return $this->response($result);
    }

    public function resendEmail(int $id){
        $result = $this->occurrenceService->resendEmail($id);

        if($result['status']) $result['message'] = "Email reenviado com sucesso";
        return $this->response($result);
    }

    

    public function update(Request $request, $id){
        $result = $this->occurrenceService->update($request, $id);

        if($result['status']) $result['message'] = "Ocorrência atualizada com sucesso";
        return $this->response($result);
    }

    public function delete($id){
        $result = $this->occurrenceService->delete($id);

        if($result['status']) $result['message'] = "Ocorrência deletada com sucesso";
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
