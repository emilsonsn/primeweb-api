<?php

namespace App\Console\Commands;

use App\Models\ContactEmail;
use App\Models\ContactPhone;
use Exception;
use Illuminate\Console\Command;
use League\Csv\Reader;

class AddPhoneAndMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-phone-and-mail';

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
        // $path = storage_path('sql/contatoEmail.csv');
        $path = storage_path('sql/contatoPhone.csv');
        
        
        if (!file_exists($path)) {
            $this->error("Arquivo não encontrado: {$path}");
            return;
        }
    
        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);
    
        foreach ($csv as $record) {
            if (!is_numeric($record['id'])) continue;
            try{
                $contactData = [
                    'id' => $record['id'],
                    // 'email' => $record['email'] ?? null,
                    'phone' => $record['phone'] ?? null,
                    // 'contact_id' => $record['contact'] ?? null,    
                    'contact_id' => $record['contact_id'] ?? null,
                ];

                // ContactEmail::create($contactData);
                ContactPhone::create($contactData);

            }catch(Exception $error){
                $errorMesage = $error->getMessage();
                $errorMesage;
            }
        }
    }
}
