<?php

namespace App\Console\Commands;

use App\Services\Routines\RoutinesService;
use Illuminate\Console\Command;

class CreateNotification extends Command
{

    protected $routinesService;

    public function __construct(RoutinesService $routinesService)
    {
        parent::__construct();
        $this->routinesService = $routinesService;
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-notification';

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
        $this->routinesService->create_notifications();
    }
}
