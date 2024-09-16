<?php

namespace App\Http\Controllers;

use App\Services\PhoneCall\PhoneCallService;
use Illuminate\Http\Request;

class PhoneCallController extends Controller
{
    private $phoneCallService;

    public function __construct(PhoneCallService $phoneCallService) {
        $this->phoneCallService = $phoneCallService;
    }

    public function all() {
        $result = $this->phoneCallService->all();

        return $this->response($result);
    }

    public function search(Request $request){
        $result = $this->phoneCallService->search($request);

        return $result;
    }

    public function create(Request $request){
        $result = $this->phoneCallService->create($request);

        if($result['status']) $result['message'] = "Telefonema criado com sucesso";
        return $this->response($result);
    }

    public function update(Request $request, $id){
        $result = $this->phoneCallService->update($request, $id);

        if($result['status']) $result['message'] = "Telefonema atualizado com sucesso";
        return $this->response($result);
    }

    public function delete($id){
        $result = $this->phoneCallService->delete($id);

        if($result['status']) $result['message'] = "Telefonema deletado com sucesso";
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
