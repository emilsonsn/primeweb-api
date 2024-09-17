<?php

namespace App\Services;

use App\Mail\OccurrenceMail;
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

            $occurrences = Occurrence::with(['user']);

            if($request->is_calendar){
                $occurrences->whereIn('status', ['PresentationVisit','SchedulingVisit','ReschedulingVisit']);
            }

            if ($request->filled('status')) {
                $occurrences->where('status', $request->status);
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
                'status' => 'required|in:Lead,PresentationVisit,ConvertedContact,SchedulingVisit,ReschedulingVisit,DelegationContact,InNegotiation,Closed,Lost',
                'link' => 'nullable|url',
                'observations' => 'nullable|string',
                'phone_call_id' => 'nullable|interger',
                'contact_id' => 'nullable|interger',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return ['status' => false, 'error' => $validator->errors(), 'statusCode' => 400];
            }

            $data = $validator->validated();
            $data['user_id'] = Auth::user()->id;

            $occurrence = Occurrence::create($data);

            if (in_array($occurrence->status, ['PresentationVisit', 'SchedulingVisit', 'ReschedulingVisit'])) {
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

    public function update($request, $id)
    {
        try {
            $occurrence = Occurrence::find($id);
            if (!$occurrence) throw new Exception('Occurrence not found');

            $rules = [                
                'date' => 'date',
                'time' => 'nullable',
                'status' => 'required|in:Lead,PresentationVisit,ConvertedContact,SchedulingVisit,ReschedulingVisit,DelegationContact,InNegotiation,Closed,Lost',
                'link' => 'nullable|url',
                'observations' => 'nullable|string'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $data = $validator->validated();
            $data['user_id'] = Auth::user()->id;

            $occurrence->update($data);

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
