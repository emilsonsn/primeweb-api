<?php

namespace App\Services\Client;

use App\Enums\ClientStatusEnum;
use App\Models\Log;
use App\Models\Client;
use App\Models\ClientContract;
use App\Models\ClientEmail;
use App\Models\ClientPhone;
use App\Models\ClientStatus;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ClientService
{
    public function all()
    {
        try {
            $clients = Client::with(['segment', 'consultant', 'seller', 'technical', 'emails', 'phones', 'contracts']);

            return ['status' => true, 'data' => $clients];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function getById($id)
    {
        try {
            $clients = Client::with([
                'segment',
                'consultant',
                'seller',
                'technical',
                'emails',
                'phones',
                'contracts',
                'status'
            ])->find($id);

            return ['status' => true, 'data' => $clients];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);
    
            $clients = Client::with([
                'segment',
                'consultant',
                'seller',
                'technical',
                'emails',
                'phones',
                'contracts',
                'status'
            ]);
    
            $auth = Auth::user();
    
            if ($auth->role == 'Seller') {
                $clients->where('seller_id', $auth->id);
            }
    
            if ($auth->role == 'Technical') {
                $clients->where('technical_id', $auth->id);
            }
    
            if ($auth->role == 'Consultant') {
                $clients->where('consultant_id', $auth->id);
            }
    
            if ($request->filled('company')) {
                $clients->where('company', 'LIKE', "%{$request->company}%");
            }
    
            if ($request->filled('segment_id')) {
                $clients->where('segment_id', $request->segment_id);
            }
    
            if ($request->filled('technical_id')) {
                $clients->where('technical_id', $request->technical_id);
            }
    
            if ($request->filled('domain')) {
                $clients->where('domain', 'LIKE', "%{$request->domain}%");
            }
    
            if ($request->filled('status')) {
                $clients->where('status', $request->status);
            }
    
            $clients = $clients->paginate($perPage);
    
            return $clients;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }    

    public function create($request)
    {
        try {
            $rules = [
                'company' => 'required|string|max:255',
                'client_responsable_name' => 'required|string|max:255',
                'client_responsable_name_2' => 'required|string|max:255',
                'domain' => 'required|string|max:255',
                'cnpj' => 'required|string|max:14',
                'cep' => 'required|string|max:9',
                'street' => 'required|string|max:255',
                'neighborhood' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:2',
                'monthly_fee' => 'required|numeric',
                'payment_first_date' => 'required|date',
                'duedate_day' => 'required|integer|min:1|max:31',
                'final_date' => 'nullable|date',
                'segment_id' => 'required|exists:segments,id',
                'consultant_id' => 'required|exists:users,id',
                'seller_id' => 'required|exists:users,id',
                'technical_id' => 'required|exists:users,id',
                'observations' => 'required|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return ['status' => false, 'error' => $validator->errors(), 'statusCode' => 400];
            }

            $data = $validator->validated();
            
            $client = Client::create($data);

            if(isset($request->emails) && count($request->emails)){
                foreach($request->emails as $emailData){
                    ClientEmail::updateOrCreate(
                    [
                        'id' => $emailData['id'],
                    ], [
                        'name' => $emailData['name'],
                        'email' => $emailData['email'],
                        'client_id' => $client->id
                    ]);
                }
            }

            if(isset($request->phones) && count($request->phones)){
                foreach($request->phones as $phoneData){
                    ClientPhone::updateOrCreate(
                    [
                        'id' => $phoneData['id'],
                    ], [
                        'name' => $phoneData['name'],
                        'phone' => $phoneData['phone'],
                        'client_id' => $client->id
                    ]);
                }
            }

            $client['clientStatus'] = ClientStatus::create([
                'status' => ClientStatusEnum::IN_PROGRESS->value,
                'date' => Carbon::now(),
                'client_id' => $client->id
            ]);

            Log::create([
                'user_id' => Auth::user()->id,
                'ip' => request()->ip(),
                'action' => "Criou o cliente {$client->company} ($client->id)"
            ]);

            return ['status' => true, 'data' => $client];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function changeStatus($request)
    {
        try {
            $rules = [
                'client_id' => 'required|exists:clients,id',
                'status' => ['required', 'in:' . implode(',', array_column(ClientStatusEnum::cases(), 'value'))],
                'date' => 'nullable|date',
            ];
    
            $validator = Validator::make($request->all(), $rules);
    
            if ($validator->fails()) {
                return ['status' => false, 'error' => $validator->errors(), 'statusCode' => 400];
            }
    
            $client = Client::find($request->client_id);
    
            if (!isset($client)) throw new Exception('Cliente não encontrado');
    
            $clientStatus = ClientStatus::create([
                'status' => $request->status,
                'date' => $request->date ?? Carbon::now(),
                'client_id' => $client->id
            ]);
    
            return ['status' => true, "data" => $clientStatus];
    
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function update($request, $client_id)
    {
        try {
            $rules = [
                'client_responsable_name' => 'required|string|max:255',
                'client_responsable_name_2' => 'required|string|max:255',
                'company' => 'required|string|max:255',
                'domain' => 'required|string|max:255',
                'cnpj' => 'required|string|max:14',
                'cep' => 'required|string|max:9',
                'street' => 'required|string|max:255',
                'neighborhood' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:2',
                'monthly_fee' => 'required|numeric',
                'payment_first_date' => 'required|date',
                'duedate_day' => 'required|integer|min:1|max:31',
                'final_date' => 'nullable|date',
                'segment_id' => 'required|exists:segments,id',
                'consultant_id' => 'required|exists:users,id',
                'seller_id' => 'required|exists:users,id',
                'technical_id' => 'required|exists:users,id',
                'observations' => 'required|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                throw new Exception($validator->errors());
            }

            $client = Client::find($client_id);

            if (!$client) throw new Exception('Cliente não encontrado');

            $client->update($validator->validated());

            if(isset($request->emails) && count($request->emails)){
                foreach($request->emails as $emailData){
                    ClientEmail::updateOrCreate(
                    [
                        'id' => $emailData['id'],
                    ], [
                        'name' => $emailData['name'],
                        'email' => $emailData['email'],
                        'client_id' => $client->id
                    ]);
                }
            }

            if(isset($request->phones) && count($request->phones)){
                foreach($request->phones as $phoneData){
                    ClientPhone::updateOrCreate(
                    [
                        'id' => $phoneData['id'],
                    ], [
                        'name' => $phoneData['name'],
                        'phone' => $phoneData['phone'],
                        'client_id' => $client->id
                    ]);
                }
            }

            Log::create([
                'user_id' => Auth::user()->id,
                'action' => "Editou o cliente {$client->company} ($client->id)",
                'ip' => request()->ip()
            ]);

            return ['status' => true, 'data' => $client];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function delete($client_id)
    {
        try {
            $client = Client::find($client_id);

            if (!$client) throw new Exception('Cliente não encontrado');

            $company = $client->company;
            $id = $client->id;
            
            $client->delete();

            Log::create([
                'user_id' => Auth::user()->id,
                'action' => "Deletou o cliente {$company} ($id)",
                'ip' => request()->ip()
            ]);

            return ['status' => true];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function deleteEmail($id){
        try{
            $email = ClientEmail::find($id);

            if(!isset($email)) throw new Exception('Email não encontrado!');

            $emailName = $email->name;
            $emailId = $email->id;
            
            $email->delete();

            Log::create([
                'user_id' => Auth::user()->id,
                'action' => "Deletou o email {$emailName} ($emailId)",
                'ip' => request()->ip()
            ]);

            return ['status' => true, 'data' => $emailName];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function deletePhone($id){
        try{
            $phone = ClientPhone::find($id);

            if(!isset($phone)) throw new Exception('Telefone não encontrado!');

            $phoneName = $phone->name;
            $phoneId = $phone->name;

            $phone->delete();

            Log::create([
                'user_id' => Auth::user()->id,
                'action' => "Deletou o telefone {$phoneName} ($phoneId)",
                'ip' => request()->ip()
            ]);

            return ['status' => true, 'data' => $phoneName];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function addContract($request, $client_id)
{
    try {
        $rules = [
            'number' => 'required|string|max:255',
            'contract' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'date_hire' => 'required|date',
            'number_words_contract' => 'required|integer',
            'service_type' => 'required|in:PLAN_A,PLAN_B_SILVER,PLAN_B_GOLD',
            'model' => 'required|in:V1,V2,V3,V4,V5,CLIENT_LAYOUT,CUSTOMIZED,N1,N2,N3',
            'observations' => 'required|string'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['status' => false, 'error' => $validator->errors(), 'statusCode' => 400];
        }

        $client = Client::find($client_id);

        if (!$client) throw new Exception('Cliente não encontrado');

        if ($request->hasFile('contract')) {
            $file = $request->file('contract');
            $path = $file->store('contracts', 'public'); 
        } else {
            throw new Exception('Arquivo do contrato não foi enviado.');
        }

        $data = $validator->validated();
        $data['client_id'] = $client_id;
        $data['path'] = $path;

        $contract = ClientContract::create($data);

        Log::create([
            'user_id' => Auth::user()->id,
            'ip' => request()->ip(),
            'action' => "Adicionou o contrato {$contract->number} ao cliente {$client->company} ($client->id)"
        ]);

        return ['status' => true, 'data' => $contract];
    } catch (Exception $error) {
        return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
    }
}


    public function deleteContract($contract_id)
    {
        try {
            $contract = ClientContract::find($contract_id);

            if (!$contract) throw new Exception('Contrato não encontrado');

            $client = $contract->client;
            $number = $contract->number;
            $filePath = $contract->path;

            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            $contract->delete();

            Log::create([
                'user_id' => Auth::user()->id,
                'action' => "Deletou o contrato {$number} do cliente {$client->company} ({$client->id})",
                'ip' => request()->ip()
            ]);

            return ['status' => true];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
}

}