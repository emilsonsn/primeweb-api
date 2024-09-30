<?php

namespace App\Services\Dashboard;

use App\Enums\PurchaseStatusEnum;
use App\Enums\SolicitationStatusEnum;
use App\Models\Contact;
use App\Models\Occurrence;
use App\Models\Order;
use App\Models\PhoneCall;
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
            $phoneCalls = PhoneCall::count();

            $contactMonth = Contact::whereDate('created_at', now())->count();

            $data = [
                'occurrencesMonth' => $occurrencesMonth,
                'contacts' => $contacts,
                'phoneCalls' => $phoneCalls,
                'contactMonth' => $contactMonth,
            ];

            return ['status' => 200, 'data' => $data];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

}
