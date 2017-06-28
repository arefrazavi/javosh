<?php

namespace App\Console\Commands;

use App\Libraries\SentenceLib;
use Illuminate\Console\Command;

class ComputeSentenceAspectFrequency extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sentence:compute-aspect-freq';

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
        SentenceLib::computeAspectFrequency();
    }
}