<?php

namespace App\Console\Commands;

use App\Libraries\WordLib;
use App\Models\Word;
use Illuminate\Console\Command;

class UpdatePosTag extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'word:update-pos-tag';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    protected $wordLib;

    /**
     * Create a new command instance.
     *
     * @return void
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
        $files = [
            [
                'filePath' => base_path('data/words/verbs.csv'),
                'posTag' => 'V'
            ],

            [
                'filePath' => base_path('data/words/positive-attributes.csv'),
                'posTag' => 'ADJ'
            ],

            [
                'filePath' => base_path('data/words/negative-attributes.csv'),
                'posTag' => 'ADJ'
            ]
        ];

        $this->wordLib->updatePosTag($files);



    }
}
