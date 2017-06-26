<?php

namespace App\Console\Commands;

use App\Libraries\ResultLib;
use Illuminate\Console\Command;

class StoreResults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'result:store';

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

        $fileDir = base_path("data/results/*.csv");

        ResultLib::storeResults($fileDir);

    }
}
