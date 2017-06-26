<?php

namespace App\Console\Commands;

use App\Http\Controllers\WordController;
use App\Libraries\WordLib;
use Illuminate\Console\Command;

class StoreWords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'word:store';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store words in the comments';

    protected $wordLib;

    /**
     * StoreWords constructor.
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
        $this->wordLib->storeWords();
        //$this->wordLib->storeWord("لگ");
        //WordLib::resolveNonStopWords();
    }
}
