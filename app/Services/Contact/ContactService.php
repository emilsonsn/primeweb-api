<?php

namespace App\Services\Contact;

use App\Enums\RolesEnum;
use App\Models\Contact;
use App\Models\ContactPhone;
use App\Models\ContactEmail;
use App\Models\ContactSegment;
use App\Models\Log;
use App\Models\Occurrence;
use App\Models\PhoneCall;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ContactService
{
    public function all()
    {
        try {
            $contacts = Contact::with(['phones', 'emails', 'segments', 'user'])->get();

            return ['status' => true, 'data' => $contacts];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);
            $contacts = Contact::
                with([
                    'phones',
                    'emails',
                    'segments',
                    'occurrences',
                    'user'
                ])->orderBy('id', 'desc');

            $auth = Auth::user();

            $is_seller = $auth->role == RolesEnum::Seller->value;

            $contacts->when($is_seller, function ($query) use ($auth) {
                $query->where('user_id', $auth->id);
            });

            if(isset($request->date_from) && isset($request->date_to)){
                if($request->date_from === $request->date_to){
                    $contacts->whereDate('return_date', $request->date_from);
                }else{
                    $contacts->whereBetween('return_date',[$request->date_from, $request->date_to]);
                }
            }

            if ($request->filled('company')) {
                $contacts->where('company', 'LIKE', "%{$request->company}%");
            }
            if ($request->filled('name')) {
                $contacts->where('name', 'LIKE', "%{$request->name}%");
            }
            if ($request->filled('email')) {
                $contacts->whereHas('emails', function ($query) use ($request) {
                    $query->where('email', 'LIKE', "%{$request->email}%");
                });
            }
            if ($request->filled('phone')) {
                $contacts->whereHas('phones', function ($query) use ($request) {
                    $query->where('phone', 'LIKE', "%{$request->phone}%");
                });
            }
            if ($request->filled('domain')) {
                $contacts->where('domain', 'LIKE', "%{$request->domain}%");
            }
            if ($request->filled('origin')) {
                $contacts->where('origin', 'LIKE', "%{$request->origin}%");
            }
            if ($request->filled('status')) {
                $contacts->where('status', $request->status);
            }
            if ($request->filled('responsible')) {
                $contacts->where('responsible', 'LIKE', "%{$request->responsible}%");
            }

            $contacts = $contacts->paginate($perPage);

            return $contacts;
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
                'domain' => 'required|string|max:255',
                'responsible' => 'required|string|max:255',
                'origin' => 'required|string|max:255',
                'return_date' => 'required|date',
                'return_time' => 'required|date_format:H:i',
                'cnpj' => 'required|string',
                'cep' => 'nullable|string',
                'street' => 'nullable|string',
                'number' => 'nullable|string',
                'complement' => 'nullable|string',                
                'neighborhood' => 'nullable|string',
                'city' => 'nullable|string',
                'state' => 'nullable|string',
                'observations' => 'nullable|string',
                'phones' => 'array',
                'emails' => 'array',
                'segments' => 'array',
                'phone_call_id' => 'nullable|integer',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return ['status' => false, 'error' => $validator->errors(), 'statusCode' => 400];
            }

            $data = $validator->validated();
            $data['user_id'] = $data['user_id'] ?? Auth::user()->id;

            $contact = Contact::create($data);
            
            if ($request->phones != 'null') {
                $phones = !is_array($request->phones) ? json_decode($request->phones, true) : $request->phones;
                foreach ($phones as $phone) {
                    ContactPhone::create([
                        'phone' => $phone['phone'],
                        'contact_id' => $contact->id
                    ]);
                }
            }
            
            if ($request->emails != 'null') {
                $emails = !is_array($request->emails) ? json_decode($request->emails, true) : $request->emails;
                foreach ($emails as $email) {
                    ContactEmail::create([
                        'email' => $email['email'],
                        'contact_id' => $contact->id
                    ]);
                }
            }

            if ($request->segments != 'null') {
                $segments = !is_array($request->segments) ? json_decode($request->segments, true) : $request->segments;
                foreach ($segments as $segment) {
                    ContactSegment::create([
                        'segment_id' => $segment['id'],
                        'contact_id' => $contact->id
                    ]);
                }
            }

            if(isset($request->phone_call_id)){
                $phone_call_id = $request->phone_call_id;
                Occurrence::where('phone_call_id', $phone_call_id)
                    ->update([
                        'contact_id' => $contact->id,                        
                    ]);

                PhoneCall::find($phone_call_id)->delete();
            }

            Log::create([
                'user_id' => Auth::user()->id,
                'action' => "Cadastrou um contato {$request->company}(#{{$contact->id}})",
                'ip' => request()->ip()
            ]);

            return ['status' => true, 'data' => $contact];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function update($request, $contact_id)
    {
        try {
            $rules = [
                'user_id' => 'nullable|exists:users,id',
                'company' => 'required|string|max:255',
                'domain' => 'required|string|max:255',
                'responsible' => 'required|string|max:255',
                'origin' => 'required|string|max:255',
                'return_date' => 'required|date',
                'return_time' => 'required|date_format:H:i',
                'cnpj' => 'required|string',
                'cep' => 'nullable|string',
                'street' => 'nullable|string',
                'number' => 'nullable|string',
                'complement' => 'nullable|string',
                'neighborhood' => 'nullable|string',
                'city' => 'nullable|string',
                'state' => 'nullable|string',
                'observations' => 'nullable|string',
                'phones' => 'array',
                'emails' => 'array',
                'segments' => 'array',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                throw new Exception($validator->errors());
            }

            $contact = Contact::find($contact_id);

            if (!$contact) throw new Exception('Contato não encontrado');

            $data = $validator->validated();
            $data['user_id'] = $data['user_id'] ?? Auth::user()->id;

            $contact->update($data);

            DB::transaction(function () use ($request, $contact) {

                ContactPhone::where('contact_id', $contact->id)->delete();
                if ($request->phones) {
                    foreach ($request->phones as $phone) {
                        ContactPhone::create([
                            'phone' => $phone['phone'],
                            'contact_id' => $contact->id
                        ]);
                    }
                }

                ContactEmail::where('contact_id', $contact->id)->delete();
                if ($request->emails) {
                    foreach ($request->emails as $email) {
                        ContactEmail::create([
                            'email' => $email['email'],
                            'contact_id' => $contact->id
                        ]);
                    }
                }

                ContactSegment::where('contact_id', $contact->id)->delete();
                if ($request->segments) {
                    foreach ($request->segments as $segment) {
                        ContactSegment::create([
                            'segment_id' => $segment['id'],
                            'contact_id' => $contact->id
                        ]);
                    }
                }
            });

            Log::create([
                'user_id' => Auth::user()->id,
                'action' => "Editou um contato {$request->company}(#{{$contact->id}})",
                'ip' => request()->ip()
            ]);

            return ['status' => true, 'data' => $contact];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function delete($contact_id)
    {
        try {
            $contact = Contact::find($contact_id);

            if (!$contact) throw new Exception('Contato não encontrado');

            $company = $contact->company;
            $id = $contact->id;
            $contact->delete();

            Log::create([
                'user_id' => Auth::user()->id,
                'action' => "Apagou o contato {$company}(#{{$id}})",
                'ip' => request()->ip()
            ]);

            return ['status' => true];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function delete_phone($id)
    {
        try {
            $contactPhone = ContactPhone::find($id);

            if (!$contactPhone) throw new Exception('Telefone não encontrado');

            $contact = $contactPhone->contact;
            $contactPhone->delete();

            Log::create([
                'user_id' => Auth::user()->id,
                'action' => "Apagou o telefone do contato {$contact->company}(#{{$contact->id}})",
                'ip' => request()->ip()
            ]);

            return ['status' => true];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function delete_email($id)
    {
        try {
            $contactEmail = ContactEmail::find($id);

            if (!$contactEmail) throw new Exception('Email não encontrado');

            $contact = $contactEmail->contact;
            $contactEmail->delete();

            Log::create([
                'user_id' => Auth::user()->id,
                'action' => "Apagou o telefone do contato {$contact->company}(#{{$contact->id}})",
                'ip' => request()->ip()
            ]);

            return ['status' => true];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function delete_segment($id)
    {
        try {
            $contactSegment = ContactSegment::find($id);

            if (!$contactSegment) throw new Exception('Segmento não encontrado');

            $contact = $contactSegment->contact;
            $contactSegment->delete();

            Log::create([
                'user_id' => Auth::user()->id,
                'action' => "Apagou o telefone do contato {$contact->company}(#{{$contact->id}})",
                'ip' => request()->ip()
            ]);

            return ['status' => true];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
}