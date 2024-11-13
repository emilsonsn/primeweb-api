<?php

namespace App\Console\Commands;

use App\Models\Occurrence;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use League\Csv\Reader;

class addOcorrence extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-ocorrence';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = storage_path('sql/occurrence.csv');
        
        if (!file_exists($path)) {
            $this->error("Arquivo não encontrado: {$path}");
            return;
        }
    
        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0); // Define a primeira linha como cabeçalho
    
        $status = [
            '1' => 'Lead',
            '2' => 'PresentationVisit',
            '3' => 'ReschedulingVisit',
            '4' => 'DelegationContact',
            '5' => 'InNegotiation',
            '6' => 'Closed',
            '7' => 'Lost',            
        ];
    
        foreach ($csv as $record) {
            if (!is_numeric($record['id'])) continue;

            $date = isset($record['date']) ? Carbon::createFromFormat('d/m/Y', $record['date'])->format('Y-m-d') : Carbon::now()->format('Y-m-d');

            try{
                $user = [
                    'id' => $record['id'] ?? null,
                    'user_id' => $record['user_id'] ?? null,
                    'phone_call_id' => null,
                    'contact_id' => $record['contact_id'] ?? null,
                    'date' => $date ?? null,
                    'time' => $record['time'] ?? null,
                    'status' => $status[$record['status']] ?? null,
                    'link' => '----',
                    'observations' => $record['observations'] ?? null,
                ];
                    
                Occurrence::create($user);
            }catch(Exception $error){
                $errorMessage = $error->getMessage();
                $errorMessage;
            }
        }
    }
}
