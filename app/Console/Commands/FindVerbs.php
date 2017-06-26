<?php

namespace App\Console\Commands;

use App\Libraries\WordLib;
use Illuminate\Console\Command;

class FindVerbs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'word:find-verbs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $wordLib;

    /**
     * FindVerbs constructor.
     * @param WordLib $wordLib
     */
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
        $this->wordLib->findVerbs();
    }
}
