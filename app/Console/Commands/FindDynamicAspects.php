<?php

namespace App\Console\Commands;

use App\Libraries\AspectLib;
use Illuminate\Console\Command;

class FindDynamicAspects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aspect:find';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
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
        AspectLib::findFrequentItemSets();
        AspectLib::findDynamicAspects();
    }
}
