<?php

namespace App\Console\Commands;

use App\Libraries\AspectLib;
use App\Models\Aspect;
use Illuminate\Console\Command;

class StoreAspects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aspect:store';

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
        $baseDir = 'data/aspects/';
        AspectLib::storeAspects($baseDir);

    }
}
