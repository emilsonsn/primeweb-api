<?php

namespace App\Services\ClientWordKey;

use App\Models\Log;
use App\Models\Client;
use App\Models\ClientContract;
use App\Models\ClientEmail;
use App\Models\ClientPhone;
use App\Models\ClientWordKey;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ClientWordKeyService
{
    public function all()
    {
        try {
            $clientWordKeys = ClientWordKey::with(['client', 'user']);

            return ['status' => true, 'data' => $clientWordKeys];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);
            $client_id = $request->client_id;

            $clientWordKeys = ClientWordKey::with(['client', 'user']);

            if (isset($client_id)) {
                $clientWordKeys->where('client_id', $client_id);
            }

            $clientWordKeys = $clientWordKeys->paginate($perPage);

            return $clientWordKeys;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function create($request)
    {
        try {
            $rules = [
                'word_key' => ['required', 'string', 'max:255'],
                'client_id' => ['required', 'integer'],
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return ['status' => false, 'error' => $validator->errors(), 'statusCode' => 400];
            }

            $data = $validator->validated();

            $data['user_id'] = Auth::user()->id;
            
            $clientWordKey = ClientWordKey::create($data);

            Log::create([
                'user_id' => Auth::user()->id,
                'ip' => request()->ip(),
                'action' => "Criou a palavra chave {$clientWordKey->work_key} para o cliente {$clientWordKey->client->name}"
            ]);

            return ['status' => true, 'data' => $clientWordKey];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }


    public function delete($clientWordKeId)
    {
        try {
            $clientWordKey = ClientWordKey::find($clientWordKeId);

            if (!$clientWordKey) throw new Exception('Palavra chave não encontrada');

            $word_key = $clientWordKey->word_key;
            $clientName = $$clientWordKey->client->name;
            
            $clientWordKey->delete();

            Log::create([
                'user_id' => Auth::user()->id,
                'action' => "Deletou a palavra chave {$word_key} do cliente ($clientName)",
                'ip' => request()->ip()
            ]);

            return ['status' => true];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
}