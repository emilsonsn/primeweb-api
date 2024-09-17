<?php

namespace App\Services\Contact;

use App\Enums\RolesEnum;
use App\Models\Contact;
use App\Models\ContactPhone;
use App\Models\ContactEmail;
use App\Models\ContactSegment;
use App\Models\Log;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ContactService
{
    public function all()
    {
        try {
            $contacts = Contact::with(['phones', 'emails', 'segments'])->get();

            return ['status' => true, 'data' => $contacts];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);
            $contacts = Contact::with(['phones', 'emails', 'segments']);

            $auth = Auth::user();

            $is_seller = $auth->role == RolesEnum::Seller->value;

            $contacts->when($is_seller, function ($query) use ($auth) {
                $query->where('user_id', $auth->id);
            });

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
                'user_id' => 'required|exists:users,id',
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
                return ['status' => false, 'error' => $validator->errors(), 'statusCode' => 400];
            }

            $contact = Contact::create($validator->validated());

            if ($request->phones) {
                foreach ($request->phones as $phone) {
                    ContactPhone::create([
                        'phone' => $phone['phone'],
                        'contact_id' => $contact->id
                    ]);
                }
            }

            if ($request->emails) {
                foreach ($request->emails as $email) {
                    ContactEmail::create([
                        'email' => $email['email'],
                        'contact_id' => $contact->id
                    ]);
                }
            }

            if ($request->segments) {
                foreach ($request->segments as $segment) {
                    ContactSegment::create([
                        'segment_id' => $segment['id'],
                        'contact_id' => $contact->id
                    ]);
                }
            }

            Log::create([Auth::user()->id, "Cadastrou um contato {$request->company}(#{{$contact->id}})", request()->ip()]);

            return ['status' => true, 'data' => $contact];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function update($request, $contact_id)
    {
        try {
            $rules = [
                'user_id' => 'required|exists:users,id',
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

            $contact->update($validator->validated());

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

            Log::create([Auth::user()->id, "Editou um contato {$request->company}(#{{$contact->id}})", request()->ip()]);

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

            Log::create([Auth::user()->id, "Apagou o contato {$company}(#{{$id}})", request()->ip()]);

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

            Log::create([Auth::user()->id, "Apagou o telefone do contato {$contact->company}(#{{$contact->id}})", request()->ip()]);

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

            Log::create([Auth::user()->id, "Apagou o telefone do contato {$contact->company}(#{{$contact->id}})", request()->ip()]);

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

            Log::create([Auth::user()->id, "Apagou o telefone do contato {$contact->company}(#{{$contact->id}})", request()->ip()]);

            return ['status' => true];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
}