<?php

namespace App\Console\Commands;

use App\Libraries\AspectLib;
use App\Models\Aspect;
use Illuminate\Console\Command;

class StoreKeywords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aspect:store-keywords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $aspectLib;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(AspectLib $aspectLib)
    {
        parent::__construct();
        $this->aspectLib = $aspectLib;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->aspectLib->storeKeywords();
    }
}
