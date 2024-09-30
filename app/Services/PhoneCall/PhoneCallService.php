<?php

namespace App\Services\PhoneCall;

use App\Enums\RolesEnum;
use App\Models\Log;
use App\Models\PhoneCall;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PhoneCallService
{
    public function all()
    {
        try {
            $phoneCalls = PhoneCall::with(['user'])->all();

            return ['status' => true, 'data' => $phoneCalls];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);
            $company = $request->company;
            $domain = $request->domain;
            $phone = $request->phone;

            $phoneCalls = PhoneCall::with(['user', 'occurrences']);

            $auth = Auth::user();
            $is_seller = $auth->role == RolesEnum::Seller->value;

            $phoneCalls->when($is_seller, function ($query) use ($auth) {
                $query->where('user_id', $auth->id);
            });

            if(isset($request->date_from) && isset($request->date_to)){
                if($request->date_from === $request->date_to){
                    $phoneCalls->whereDate('return_date', $request->date_from);
                }else{
                    $phoneCalls->whereBetween('return_date',[$request->date_from, $request->data_to]);
                }
            }

            if (isset($company)) {
                $phoneCalls->where('company', 'LIKE', "%{$company}%");
            }

            if (isset($phone)) {
                $phoneCalls->where('phone', 'LIKE', "%{$phone}%");
            }

            if (isset($domain)) {
                $phoneCalls->where('domain', 'LIKE', "%{$domain}%");
            }            

            $phoneCalls = $phoneCalls->paginate($perPage);

            return $phoneCalls;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function create($request)
    {
        try {
            $rules = [                
                'user_id' => 'nullable|exists:users,id',
                'company' => 'required|string|max:255',
                'phone' => 'required|string|max:15',
                'domain' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'return_date' => 'required|date',
                'return_time' => 'required|date_format:H:i',
                'observations' => 'nullable|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return ['status' => false, 'error' => $validator->errors(), 'statusCode' => 400];
            }

            $data = $validator->validated();
            $data['user_id'] = $data['user_id'] ?? Auth::user()->id;

            $phoneCall = PhoneCall::create($data);

            Log::create([
                'user_id' => Auth::user()->id,
                'action' => "Criou um telefonema {$phoneCall->company}(#{{$phoneCall->id}})",
                'ip' => request()->ip()
            ]);

            return ['status' => true, 'data' => $phoneCall];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function update($request, $phone_call_id)
    {
        try {
            $rules = [
                'user_id' => 'nullable|exists:users,id',
                'company' => 'required|string|max:255',
                'phone' => 'required|string|max:15',
                'domain' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'return_date' => 'required|date',
                'return_time' => 'required|date_format:H:i',
                'observations' => 'nullable|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                throw new Exception($validator->errors());
            }

            $phoneCallToUpdate = PhoneCall::find($phone_call_id);

            if (!$phoneCallToUpdate) throw new Exception('Chamada telefônica não encontrada');

            $data = $validator->validated();
            $data['user_id'] = $data['user_id'] ?? Auth::user()->id;

            $phoneCallToUpdate->update($data);

            Log::create([
                'user_id' => Auth::user()->id,
                'action' => "Editou o telefonema {$phoneCallToUpdate->company}(#{{$phoneCallToUpdate->id}})",
                'ip' => request()->ip()
            ]);

            return ['status' => true, 'data' => $phoneCallToUpdate];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function delete($phone_call_id)
    {
        try {
            $phoneCall = PhoneCall::find($phone_call_id);

            if (!$phoneCall) throw new Exception('Chamada telefônica não encontrada');

            $company = $phoneCall->company;
            $id = $phoneCall->id;

            $phoneCall->delete();

            Log::create([
                'user_id' => Auth::user()->id,
                'action' => "Editou o telefonema {$company}(#{{$id}})",
                'ip' => request()->ip()
            ]);

            return ['status' => true];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
}
