<?php

namespace App\Console\Commands;

use App\Libraries\SentenceLib;
use Illuminate\Console\Command;

class StoreSentences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sentence:store';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $sentenceLib;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SentenceLib $sentenceLib)
    {
        parent::__construct();
        $this->sentenceLib = $sentenceLib;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->sentenceLib->storeSentences();
    }
}
