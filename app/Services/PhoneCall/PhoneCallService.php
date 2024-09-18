<?php

namespace App\Services\PhoneCall;

use App\Enums\RolesEnum;
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

            $phoneCalls = PhoneCall::with(['user']);

            $auth = Auth::user();
            $is_seller = $auth->role == RolesEnum::Seller->value;

            $phoneCalls->when($is_seller, function ($query) use ($auth) {
                $query->where('user_id', $auth->id);
            });

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

            $phoneCall = PhoneCall::find($phone_call_id);

            if (!$phoneCall) throw new Exception('Chamada telefônica não encontrada');

            $data = $validator->validated();
            $data['user_id'] = $data['user_id'] ?? Auth::user()->id;

            $phoneCall->update($data);

            return ['status' => true, 'data' => $phoneCall];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function delete($phone_call_id)
    {
        try {
            $phoneCall = PhoneCall::find($phone_call_id);

            if (!$phoneCall) throw new Exception('Chamada telefônica não encontrada');

            $phoneCall->delete();

            return ['status' => true];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
}
