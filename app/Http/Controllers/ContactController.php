<?php

namespace App\Http\Controllers;

use App\Services\Contact\ContactService;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    private $contactService;

    public function __construct(ContactService $contactService) {
        $this->contactService = $contactService;
    }

    public function all() {
        $result = $this->contactService->all();

        return $this->response($result);
    }

    public function search(Request $request){
        $result = $this->contactService->search($request);

        return $result;
    }

    public function create(Request $request){
        $result = $this->contactService->create($request);

        if($result['status']) $result['message'] = "Contato criado com sucesso";
        return $this->response($result);
    }

    public function update(Request $request, $id){
        $result = $this->contactService->update($request, $id);

        if($result['status']) $result['message'] = "Contato atualizado com sucesso";
        return $this->response($result);
    }

    public function delete($id){
        $result = $this->contactService->delete($id);

        if($result['status']) $result['message'] = "Contato deletado com sucesso";
        return $this->response($result);
    }

    public function delete_email($id){
        $result = $this->contactService->delete_email($id);

        if($result['status']) $result['message'] = "Email deletado com sucesso";
        return $this->response($result);
    }

    public function delete_phone($id){
        $result = $this->contactService->delete_phone($id);

        if($result['status']) $result['message'] = "Telefone deletado com sucesso";
        return $this->response($result);
    }

    public function delete_segment($id){
        $result = $this->contactService->delete_segment($id);

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