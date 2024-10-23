<?php

namespace App\Http\Controllers;

use App\Services\ClientWordKey\ClientWordKeyService;
use Illuminate\Http\Request;

class ClientWordKeyController extends Controller
{
    private $clientWordKeyService;

    public function __construct(ClientWordKeyService $clientWordKeyService) {
        $this->clientWordKeyService = $clientWordKeyService;
    }

    public function all() {
        $result = $this->clientWordKeyService->all();

        return $this->response($result);
    }

    public function search(Request $request) {
        $result = $this->clientWordKeyService->search($request);

        return $this->response($result);
    }

    public function create(Request $request) {
        $result = $this->clientWordKeyService->create($request);

        if ($result['status']) $result['message'] = "Palavra chave criada com sucesso";
        return $this->response($result);
    }

    public function delete($id) {
        $result = $this->clientWordKeyService->delete($id);

        if ($result['status']) $result['message'] = "Palavra chave deletada com sucesso";
        return $this->response($result);
    }

    private function response($result) {
        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'] ?? null,
            'data' => $result['data'] ?? null,
            'error' => $result['error'] ?? null
        ], $result['statusCode'] ?? 200);
    }
}
