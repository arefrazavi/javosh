<?php

namespace App\Console\Commands;

use App\Http\Controllers\SentenceController;
use App\Libraries\SentenceLib;
use Illuminate\Console\Command;

class ComputeSentencesEntropy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sentence:entropy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compute sentence entropy';

    protected $sentenceLib;

    /**
     * ComputeSentencesEntropy constructor.
     * @param SentenceLib $sentenceController
     */
    public function __construct(SentenceLib $sentenceController)
    {
        parent::__construct();
        $this->sentenceLib = $sentenceController;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->sentenceLib->computeSentencesEntropy();

    }
}
