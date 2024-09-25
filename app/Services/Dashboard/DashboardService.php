<?php

namespace App\Services\Dashboard;

use App\Enums\PurchaseStatusEnum;
use App\Enums\SolicitationStatusEnum;
use App\Models\Contact;
use App\Models\Occurrence;
use App\Models\Order;
use App\Models\Solicitation;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class DashboardService
{


    public function cards()
    {
        try {
            $occurrencesMonth = Occurrence::whereIn('status', ['PresentationVisit','SchedulingVisit','ReschedulingVisit'])
                ->whereMonth('date', Carbon::now())
                ->count();

            $contacts = Contact::count();

            $contacts = Contact::count();

            if (isset($user_id)) {
                $logs->where('user_id', $user_id);
            }

            if (isset($date)) {
                $logs->whereDate('created_at', $date);
            }

            $logs = $logs->paginate($perPage);

            return $logs;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

}
