<?php

namespace App\Console\Commands;

use App\Libraries\SummaryLib;
use App\Models\Category;
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
        $summariesBasePath = "data/summaries/";
        $categories = Category::fetchCategories();
        $methods = Summary::fetchMethods();
        foreach ($categories as $category) {
            foreach ($methods as $method) {
                $filePath = base_path($summariesBasePath . $method->title . "/" . $category->title . "/*.csv");
                SummaryLib::storeSummaries($filePath, $method->id);
            }
        }
    }
}
