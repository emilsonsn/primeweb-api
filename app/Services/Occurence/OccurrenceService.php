<?php

namespace App\Services\Occurence;

use App\Enums\RolesEnum;
use App\Mail\OccurrenceMail;
use App\Models\Log;
use App\Models\Occurrence;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class OccurrenceService
{
    public function all()
    {
        try {
            $occurrences = Occurrence::with(['user']);

            return ['status' => true, 'data' => $occurrences];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);

            $occurrences = Occurrence::orderBy('id', 'desc')->with(['user', 'contact', 'phone_call']);

            $auth = Auth::user();
            $is_seller = $auth->role == RolesEnum::Seller->value;

            $occurrences->when($is_seller, function ($query) use ($auth) {
                $query->where('user_id', $auth->id);
            });

            if($request->is_calendar){
                $occurrences->whereIn('status', [
                    'PresentationVisit',
                    'SchedulingVisit',
                    'ReschedulingVisit',
                    'MeetingScheduling',
                    'Meetingrescheduling',
                ]);
            }

            if ($request->filled('status')) {
                $status = explode(',', $request->status);
                $occurrences->whereIn('status', $status);
            }

            if ($request->filled('date')) {
                $occurrences->where('date', $request->date);
            }

            if ($request->filled('user_id')) {
                $occurrences->where('user_id', $request->user_id);
            }

            $occurrences = $occurrences->paginate($perPage);

            return $occurrences;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function create($request)
    {
        try {
            $rules = [
                'date' => 'required|date',
                'time' => 'required',
                'status' => 'required|in:Lead,PresentationVisit,ConvertedContact,MeetingScheduling,Meetingrescheduling,SchedulingVisit,ReschedulingVisit,DelegationContact,InNegotiation,Closed,Lost',
                'link' => 'nullable|url',
                'observations' => 'nullable|string',
                'phone_call_id' => 'nullable|integer',
                'contact_id' => 'nullable|integer',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return ['status' => false, 'error' => $validator->errors(), 'statusCode' => 400];
            }

            $data = $validator->validated();
            $data['user_id'] = Auth::user()->id;

            $occurrence = Occurrence::create($data);
            $model = $request->phone_call_id ? "telefonema ({$request->phone_call_id})" : "contato ({$request->contact_id})";

            Log::create([
                'user_id' => Auth::user()->id,
                'action' => "Criou a ocorrência para o $model",
                'ip' => request()->ip()
            ]);

            if (in_array($occurrence->status, [
                    'PresentationVisit',
                    'SchedulingVisit',
                    'ReschedulingVisit',
                    'MeetingScheduling',
                    'Meetingrescheduling',
                ])) {
                $phone = $occurrence->contact->user->phone;
                $clientName = $occurrence->contact->company;
                $url = $occurrence->link;
                $date = $occurrence->date;
                $time = $occurrence->time;

                foreach($occurrence->contact->emails as $email){
                    Mail::to($email)->send(new OccurrenceMail(
                        $phone,
                        $clientName,
                        $url,
                        $date,
                        $time
                    ));
                }
            }

            return ['status' => true, 'data' => $occurrence];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function resendEmail($id){
        try{
            $occurrence = Occurrence::find($id);
    
            if(!isset($occurrence)) throw new Exception ("Ocorrência não encontrada");
    
            $phone = $occurrence->contact->user->phone;
            $clientName = $occurrence->contact->company;
            $url = $occurrence->link;
            $date = $occurrence->date;
            $time = $occurrence->time;

            foreach($occurrence->contact->emails as $email){
                Mail::to($email)->send(new OccurrenceMail(
                    $phone,
                    $clientName,
                    $url,
                    $date,
                    $time
                ));
            }

            return ['status' => true, 'data' => $occurrence];
            
        }catch(Exception $error){
            return ["status" => false, "data" => $error->getMessage()];
        }
    }

    public function update($request, $id)
    {
        try {
            $occurrence = Occurrence::find($id);
            if (!$occurrence) throw new Exception('Occurrence not found');

            $rules = [                
                'date' => 'date',
                'time' => 'nullable',
                'status' => 'required|in:Lead,PresentationVisit,ConvertedContact,MeetingScheduling,Meetingrescheduling,SchedulingVisit,ReschedulingVisit,DelegationContact,InNegotiation,Closed,Lost',
                'link' => 'nullable|url',
                'observations' => 'nullable|string'
            ];            

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $data = $validator->validated();
            $data['user_id'] = Auth::user()->id;

            $occurrence->update($data);
            $model = $occurrence->phone_call_id ? "telefonema ({$occurrence->phone_call_id})" : "contato ({$occurrence->contact_id})";
            
            Log::create([
                'user_id' => Auth::user()->id,
                'action' => "Editou a ocorrência para o $model",
                'ip' => request()->ip()]
            );
            return ['status' => true, 'data' => $occurrence];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function delete($id)
    {
        try {
            $occurrence = Occurrence::find($id);
            if (!$occurrence) throw new Exception('Ocorrência não encontrada');

            $occurrence->delete();
            
            return ['status' => true];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
    
}
