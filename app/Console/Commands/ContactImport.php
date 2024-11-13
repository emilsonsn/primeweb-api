<?php

namespace App\Console\Commands;

use App\Models\Contact;
use App\Models\ContactSegment;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use League\Csv\Reader;

class ContactImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:contact-import';

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
        $path = storage_path('sql/contatos.csv');
        
        if (!file_exists($path)) {
            $this->error("Arquivo não encontrado: {$path}");
            return;
        }
    
        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);
    
        $origins = [
            '1' => 'Indicação',
            '2' => 'Email Marketing',
            '3' => 'Consultor',
            '4' => 'Ligação Externa',
            '5' => 'Retorno',
            '6' => 'Filtro Cliente',            
        ];
    
        foreach ($csv as $record) {
            if (!is_numeric($record['id']) || !is_numeric($record['user_id'])) continue;
            try{
                $contactData = [
                    'id' => $record['id'],
                    'user_id' => $record['user_id'] ?? null,
                    'company' => $record['company'] ?? null,
                    'domain' => $record['domain'] ?? null,
                    'responsible' => $record['responsible'] ?? null,
                    'origin' => $origins[$record['origin']] ?? null,
                    'return_date' => Carbon::now()->addYear(),
                    'return_time' => '12:00',
                    'cnpj' => $record['cnpj'] ?? null,
                    'cep' => $record['cep'] ?? null,
                    'street' => $record['street'] ?? null,
                    'number' => $record['number'] ?? null,
                    'neighborhood' => $record['neighborhood'] ?? null,
                    'city' => $record['city'] ?? null,
                    'state' => $record['state'] ?? null,
                    'observations' => $record['observations'] ?? null,
                    'segment_id' => $record['segment_id'] ?? null,
    
                ];
            }catch(Exception $error){
                $errorMesage = $error->getMessage();
                $errorMesage;
            }
            
                
            $contact = Contact::create($contactData);

            ContactSegment::create([
                'contact_id' => $contact->id,
                'segment_id' => $contactData['segment_id'],
            ]);


        }

    }
}
