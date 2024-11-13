<?php

namespace App\Console\Commands;

use App\Models\Segment;
use Illuminate\Console\Command;
use League\Csv\Reader;

class SegmentImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:segment-import';

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
        $path = storage_path('sql/segmentos.csv');
        
        if (!file_exists($path)) {
            $this->error("Arquivo não encontrado: {$path}");
            return;
        }
    
        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0); // Define a primeira linha como cabeçalho
    
    
        foreach ($csv as $record) {
            if (!is_numeric($record['id'])) continue;
            $user = [
                'id' => $record['id'],
                'name' => $record['name'],
                'user_id' =>is_numeric($record['user_id']) ? $record['user_id'] : 1,
                'status' => !!$record['status'] ? 'Active' : 'Inactive',
            ];
                
            Segment::create($user);
            
        }
    }
}
