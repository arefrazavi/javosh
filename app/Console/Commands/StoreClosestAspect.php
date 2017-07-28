<?php

namespace App\Console\Commands;

use App\Libraries\AspectLib;
use App\Models\Aspect;
use Illuminate\Console\Command;

class StoreClosestAspect extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aspect:store-closest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $baseDir = 'data/aspects/';
        AspectLib::storeClosetAspects($baseDir);
    }
}
