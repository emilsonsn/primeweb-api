<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;

class UserImport extends Command
{
    protected $signature = 'app:user-import';
    protected $description = 'Importa dados de usuários a partir de um arquivo SQL';

    public function handle()
    {
        $path = storage_path('sql/usuarios.csv');
        
        if (!file_exists($path)) {
            $this->error("Arquivo não encontrado: {$path}");
            return;
        }
    
        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0); // Define a primeira linha como cabeçalho
    
        $userTypes = [
            '1' => 'Admin',
            '2' => 'CommercialManager',
            '3' => 'Consultant',
            '4' => 'Seller',
            '5' => 'Technical',
            '6' => 'Copywriter',
            '7' => 'SocialMedia',
            '8' => 'Financial',
            '9' => 'TechnicalManager',
            '10' => 'NoAccess',
            '11' => 'CopywriterManager',
        ];
    
        foreach ($csv as $record) {
            if (!is_numeric($record['id'])) continue;
            $user = [
                'id' => $record['id'],
                'name' => $record['name'],
                'email' => $record['email'],
                'password' => Hash::make($record['password']),
                'phone' => $record['phone'],
                'cep' => strlen($record['cep']) <= 10 ? $record['cep'] : null,
                'street' => $record['street'],
                'number' => $record['number'],
                'neighborhood' => $record['neighborhood'],
                'city' => $record['city'],
                'state' => $record['state'],
                'role' => $userTypes[$record['role']] ?? 'NoAccess',
            ];
                
            User::create($user);
        }

    }
}
