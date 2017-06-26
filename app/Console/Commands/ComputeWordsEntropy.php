<?php

namespace App\Console\Commands;

use App\Libraries\WordLib;
use Illuminate\Console\Command;

class ComputeWordsEntropy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'word:compute-entropy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compute word entropy';

    protected $wordLib;

    public function __construct(WordLib $wordLib)
    {
        parent::__construct();
        $this->wordLib = $wordLib;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->wordLib->computeWordsEntropy();
    }
}
