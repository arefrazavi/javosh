<?php

namespace App\Console\Commands;

use App\Libraries\SummaryLib;
use App\Models\Summary;
use Illuminate\Console\Command;

class StoreSummaries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'summary:store';

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
        $filePaths = [
            [
                'filePath' => base_path("data/summary/Mobile-Phone/CB/*.csv"),
                'method_id' => Summary::CB_METHOD_ID,
            ],
            [
                'filePath' => base_path("data/summary/Mobile-Phone/SCB/*.csv"),
                'method_id' => Summary::SCB_METHOD_ID,
            ],
            [
                'filePath' => base_path("data/summary/Mobile-Phone/Random/*.csv"),
                'method_id' => Summary::RANDOM_METHOD_ID,
            ],


        ];

        SummaryLib::storeSummaries($filePaths);


    }
}
