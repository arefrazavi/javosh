<?php

namespace App\Console\Commands;

use App\Libraries\CommentLib;
use Illuminate\Console\Command;

class GenerateWord2Vec extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'comment:word2vec';

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
        $word2VecDir = base_path("data/word2vec/");

        CommentLib::generateWord2VecInput($word2VecDir);

        echo exec('whoami');

    }
}
