<?php

namespace App\Http\Controllers;

use App\Services\Client\ClientService;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    private $clientService;

    public function __construct(ClientService $clientService) {
        $this->clientService = $clientService;
    }

    public function all() {
        $result = $this->clientService->all();

        return $this->response($result);
    }

    public function getById(Request $request){
        $result = $this->clientService->getById($request);

        return $result;
    }

    public function search(Request $request){
        $result = $this->clientService->search($request);

        return $result;
    }

    public function create(Request $request){
        $result = $this->clientService->create($request);

        if($result['status']) $result['message'] = "Cliente criado com sucesso";
        return $this->response($result);
    }

    public function changeStatus(Request $request){
        $result = $this->clientService->changeStatus($request);

        if($result['status']) $result['message'] = "Status atualizado com sucesso";
        return $this->response($result);
    }
    
    public function update(Request $request, $id){
        $result = $this->clientService->update($request, $id);

        if($result['status']) $result['message'] = "Cliente atualizado com sucesso";
        return $this->response($result);
    }

    public function delete($id){
        $result = $this->clientService->delete($id);

        if($result['status']) $result['message'] = "Cliente deletado com sucesso";
        return $this->response($result);
    }

    public function addContract(Request $request, $client_id){
        $result = $this->clientService->addContract($request, $client_id);

        if($result['status']) $result['message'] = "Contrato adicionado com sucesso";
        return $this->response($result);
    }

    public function deleteContract($contract_id){
        $result = $this->clientService->deleteContract($contract_id);

        if($result['status']) $result['message'] = "Contrato deletado com sucesso";
        return $this->response($result);
    }


    public function deleteEmail($email_id){
        $result = $this->clientService->deleteEmail($email_id);

        if($result['status']) $result['message'] = "E-mail deletado com sucesso";
        return $this->response($result);
    }

    public function deletePhone($phone_id){
        $result = $this->clientService->deletePhone($phone_id);

        if($result['status']) $result['message'] = "Telefone deletado com sucesso";
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
