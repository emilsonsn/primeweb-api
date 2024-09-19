<?php

namespace App\Services\Routines;

use App\Models\Notification;
use App\Models\Occurrence;
use App\Models\PhoneCall;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class RoutinesService
{

    public function create_notifications()
    {
        try {
            $phoneCalls = PhoneCall::whereDate('return_date', Carbon::now())->get();

            foreach($phoneCalls as $phoneCall){
                Notification::create([
                    'user_id' => $phoneCall->user_id,
                    'message' => "{$phoneCall->company} | Telefonema hoje às {$phoneCall->return_time}",
                    'is_seen' => false,
                ]);
            }

            $occurrences = Occurrence::whereIn('status', ["PresentationVisit", "SchedulingVisit", "ReschedulingVisit"])
                ->whereDate('date', Carbon::now())
                ->get();

            $statusTranslation = [
                "PresentationVisit" =>  'visita de Apresentação',
                "SchedulingVisit" =>  'agendamento de Visita',
                "ReschedulingVisit" =>  'reagendamento de Visita',
            ];

            foreach($occurrences as $occurrence){
                $status = $statusTranslation[$occurrence->status];
                Notification::create([
                    'user_id' => $occurrence->user_id,
                    'message' => "{$occurrence->contact->company} | $status hoje às {$occurrence->time}",
                    'is_seen' => false,
                ]);
            }
            
            return ['status' => true];
        } catch (Exception $error) {
            Log::error($error->getMessage(), ['exception' => $error]);
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }        
    }
}
